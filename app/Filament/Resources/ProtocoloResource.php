<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProtocoloResource\Pages;

use App\Models\Protocolo;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ViewField;
use App\Models\ProtocoloMensagem;

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
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Atendimentos';
    protected static ?string $navigationLabel = 'Protocolos';

    

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Section::make('Dados do Protocolo')
                ->schema([
                    TextInput::make('protocolo')
                        ->disabled()
                        ->label('Número do Protocolo'),

                    TextInput::make('nome')
                        ->disabled()
                        ->label('Nome'),

                    TextInput::make('email')
                        ->disabled()
                        ->label('E-mail'),

                    TextInput::make('assunto')
                        ->label('Assunto')
                        ->required(),

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
                    // Mostra o histórico de mensagens dentro do form
                    ViewField::make('mensagens')
                        ->view('filament.forms.protocolo-mensagens'),

                    // Campo para nova resposta do atendente
                    Textarea::make('nova_mensagem')
                        ->label('Responder')
                        ->rows(3)
                        ->placeholder('Digite sua resposta...')
                        ->dehydrated(false), // evita salvar no campo errado
                ]),
        ]);
    }


    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('protocolo')->label('Protocolo')->sortable(),
                TextColumn::make('nome'),
                TextColumn::make('email'),
                TextColumn::make('assunto'),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->label('Criado em')->dateTime('d/m/Y H:i'),
            ])
            ->defaultSort('id', 'desc')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProtocolos::route('/'),
            'create' => Pages\CreateProtocolo::route('/create'),
            'edit' => Pages\EditProtocolo::route('/{record}/edit'),
        ];
    }
}
