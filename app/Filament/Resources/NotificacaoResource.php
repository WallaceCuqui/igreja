<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificacaoResource\Pages;
use App\Models\Notificacao;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

//Para verificar as permissões
use App\Filament\Resources\Traits\HasModuleAccess;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;

class NotificacaoResource extends Resource
{

    // só define qual módulo será usado
    // o nome do módulo deve ser igual ao definido em config/modules.php
    use HasModuleAccess;
    protected static string $moduleForAccess = 'notificacoes';

    protected static ?string $model = Notificacao::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationLabel = 'Notificações';
    protected static ?string $pluralModelLabel = 'Notificações';
    protected static ?string $modelLabel = 'Notificação';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('titulo')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),

                Textarea::make('mensagem')
                    ->label('Mensagem')
                    ->rows(4)
                    ->required(),

                Select::make('target_user_id')
                    ->label('Usuário alvo (opcional)')
                    ->relationship('targetUser', 'name')
                    ->preload()
                    ->searchable()
                    ->placeholder('Todos os usuários (broadcast)')
                    ->nullable(),

                DateTimePicker::make('starts_at')
                    ->label('Visível a partir de')
                    ->nullable(),

                DateTimePicker::make('ends_at')
                    ->label('Expira em')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titulo')->label('Título')->wrap()->searchable(),
                TextColumn::make('mensagem')->label('Mensagem')->limit(60),
                TextColumn::make('targetUser.name')->label('Alvo')->toggleable(),
                TextColumn::make('creator.name')->label('Criado por')->toggleable(),
                BadgeColumn::make('starts_at')
                    ->label('Status')
                    ->formatStateUsing(function ($state, $record) {
                        $now = now();
                        if ($record->starts_at && $record->starts_at->isFuture()) {
                            return 'Aguardando';
                        }
                        if ($record->ends_at && $record->ends_at->isPast()) {
                            return 'Expirada';
                        }
                        return 'Ativa';
                    })
                    ->colors([
                        'secondary' => 'Aguardando',
                        'danger' => 'Expirada',
                        'success' => 'Ativa',
                    ]),
                TextColumn::make('created_at')->label('Criada')->dateTime(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotificacoes::route('/'),
            'create' => Pages\CreateNotificacao::route('/create'),
            'edit' => Pages\EditNotificacao::route('/{record}/edit'),
        ];
    }
}
