<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProtocoloResource\Pages;
use App\Filament\Resources\ProtocoloResource\RelationManagers\ProtocoloMensagensRelationManager;
use App\Models\Protocolo;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Auth;


//Para verificar as permissões
use App\Filament\Resources\Traits\HasModuleAccess;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;


class ProtocoloResource extends Resource
{

    // só define qual módulo será usado
    // o nome do módulo deve ser igual ao definido em config/modules.php
    use HasModuleAccess;
    protected static string $moduleForAccess = 'protocolos';

    protected static ?string $model = Protocolo::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationLabel = 'Protocolos';
    protected static ?string $pluralModelLabel = 'Protocolos';
    protected static ?string $modelLabel = 'Protocolo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dados do Protocolo')
                    ->schema([
                        TextInput::make('protocolo')->disabled()->label('Número do Protocolo'),
                        TextInput::make('nome')->disabled()->label('Nome'),
                        TextInput::make('email')->disabled()->label('E-mail'),
                        TextInput::make('assunto')->label('Assunto')->required(),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'aberto' => 'Aberto',
                                'em_atendimento' => 'Em atendimento',
                                'concluido' => 'Concluído',
                                'cancelado' => 'Cancelado',
                            ])
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Mensagens')
                    ->schema([
                        Placeholder::make('mensagens')
                            ->label('Histórico')
                            ->content(function ($record) {
                                if (! $record) {
                                    return 'Nenhum registro';
                                }

                                $mensagens = $record->mensagens ?? collect();

                                if ($mensagens->isEmpty()) {
                                    return 'Nenhuma mensagem encontrada';
                                }

                                $html = '<div class="space-y-3">';
                                foreach ($mensagens as $m) {
                                    $autor = $m->user->name ?? ($m->is_staff ? 'Atendente' : 'Usuário');
                                    $data = $m->created_at ? $m->created_at->format('d/m/Y H:i') : '';
                                    $conteudo = e($m->mensagem);
                                    $html .= '<div class="p-3 rounded border">';
                                    $html .= "<div class=\"text-sm text-gray-600\">De: <strong>{$autor}</strong> — <span class=\"text-xs\">{$data}</span></div>";
                                    $html .= "<div class=\"mt-1 text-base\">{$conteudo}</div>";
                                    $html .= '</div>';
                                }
                                $html .= '</div>';

                                return new HtmlString($html);
                            }),

                        Textarea::make('nova_mensagem')
                            ->label('Responder')
                            ->rows(3)
                            ->placeholder('Digite sua resposta...')
                            ->dehydrated(false)
                            ->visibleOn('edit'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('protocolo')->label('Protocolo')->searchable(),
                TextColumn::make('nome')->label('Nome')->searchable(),
                TextColumn::make('email')->label('E-mail')->toggleable(),
                TextColumn::make('assunto')->label('Assunto')->wrap(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => [
                        'aberto' => 'Aberto',
                        'em_atendimento' => 'Em atendimento',
                        'concluido' => 'Concluído',
                        'cancelado' => 'Cancelado',
                    ][$state] ?? $state)
                    ->colors([
                        'primary' => 'aberto',
                        'warning' => 'em_atendimento',
                        'success' => 'concluido',
                        'danger' => 'cancelado',
                    ]),
                TextColumn::make('created_at')->label('Criado')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => static::canEdit($record)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => static::canDelete($record))
                    ->authorize(fn ($record) => static::canDelete($record)),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn () => static::checkAccess('delete'))
                    ->authorize(fn () => static::checkAccess('delete')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //ProtocoloMensagensRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('mensagens.user');

        $user = Auth::user();

        // Ajuste esta checagem conforme sua implementação de permissões/roles.
        // Aqui consideramos superuser quando $user->role === 'superuser'.
        // Se você tem outro modo (ex: $user->is_superuser, hasRole('superuser'), etc.)
        // substitua pela condição correta.
        $isSuperuser = $user && (property_exists($user, 'role') ? $user->role === 'superuser' : method_exists($user, 'is_superuser') && $user->is_superuser());

        if ($isSuperuser) {
            return $query;
        }

        // usuários normais (staff) só veem protocolos sem atendido_por OU que estejam atribuídos a eles
        return $query->where(function ($q) use ($user) {
            $q->whereNull('atendido_por')
            ->orWhere('atendido_por', $user->id);
        });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProtocolos::route('/'),
            'create' => Pages\CreateProtocolo::route('/create'),
            'edit' => Pages\EditProtocolo::route('/{record}/edit'),
        ];
    }
}
