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
            TextInput::make('protocolo')->disabled()->label('Protocolo'),
            TextInput::make('nome')->disabled(),
            TextInput::make('email')->disabled(),
            TextInput::make('assunto')->required(),
            Textarea::make('mensagem')->rows(4)->disabled(),
            Select::make('status')
                ->options([
                    'aberto' => 'Aberto',
                    'em_atendimento' => 'Em atendimento',
                    'concluido' => 'Concluído',
                    'cancelado' => 'Cancelado',
                ])
                ->required(),
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
