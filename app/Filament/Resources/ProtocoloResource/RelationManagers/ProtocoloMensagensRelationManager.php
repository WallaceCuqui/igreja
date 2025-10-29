<?php

namespace App\Filament\Resources\ProtocoloResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;

class ProtocoloMensagensRelationManager extends RelationManager
{
    protected static string $relationship = 'mensagens';
    protected static ?string $recordTitleAttribute = 'mensagem';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Textarea::make('mensagem')->label('Mensagem')->required()->rows(4),
            Toggle::make('is_staff')->label('É atendente')->default(false),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Autor')->toggleable(),
                TextColumn::make('mensagem')->label('Mensagem')->wrap(),
                TextColumn::make('created_at')->label('Data')->dateTime(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
