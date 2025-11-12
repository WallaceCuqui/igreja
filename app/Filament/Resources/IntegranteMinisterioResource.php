<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IntegranteMinisterioResource\Pages;
use App\Models\IntegranteMinisterio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

//Para verificar as permissões
use App\Filament\Resources\Traits\HasModuleAccess;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;

class IntegranteMinisterioResource extends Resource
{
    // só define qual módulo será usado
    // o nome do módulo deve ser igual ao definido em config/modules.php
    use HasModuleAccess;
    protected static string $moduleForAccess = 'integrantes';

    protected static ?string $model = IntegranteMinisterio::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Ministérios';
    protected static ?string $navigationLabel = 'Integrantes';
    protected static ?string $pluralModelLabel = 'Integrantes de Ministérios';
    protected static ?string $modelLabel = 'Integrante';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ministerio_id')
                    ->label('Ministério')
                    ->relationship('ministerio', 'nome')
                    ->required(),

                Forms\Components\Select::make('membro_id')
                    ->label('Membro')
                    ->relationship('membro', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pendente' => 'Pendente',
                        'ativo' => 'Ativo',
                        'inativo' => 'Inativo',
                    ])
                    ->default('pendente')
                    ->required(),

                Forms\Components\DatePicker::make('data_entrada')
                    ->label('Data de Entrada'),

                Forms\Components\DatePicker::make('data_saida')
                    ->label('Data de Saída'),

                Forms\Components\Textarea::make('observacoes')
                    ->label('Observações')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ministerio.nome')
                    ->label('Ministério')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('membro.name')
                    ->label('Membro')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pendente',
                        'success' => 'ativo',
                        'gray' => 'inativo',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('data_entrada')
                    ->label('Entrada')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('data_saida')
                    ->label('Saída')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última atualização')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pendente' => 'Pendente',
                        'ativo' => 'Ativo',
                        'inativo' => 'Inativo',
                    ]),
            ])
            ->defaultSort('data_entrada', 'desc')
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
            'index' => Pages\ListIntegranteMinisterios::route('/'),
            'create' => Pages\CreateIntegranteMinisterio::route('/create'),
            'edit' => Pages\EditIntegranteMinisterio::route('/{record}/edit'),
        ];
    }
}
