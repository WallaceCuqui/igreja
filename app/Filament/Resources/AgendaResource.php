<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgendaResource\Pages;
use App\Models\Agenda;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

//Para verificar as permissões
use App\Filament\Resources\Traits\HasModuleAccess;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;

class AgendaResource extends Resource
{
    // só define qual módulo será usado
    // o nome do módulo deve ser igual ao definido em config/modules.php
    use HasModuleAccess;
    protected static string $moduleForAccess = 'agendas';

    protected static ?string $model = Agenda::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Ministérios';
    protected static ?string $navigationLabel = 'Agendas';
    protected static ?string $pluralModelLabel = 'Agendas';
    protected static ?string $modelLabel = 'Agenda';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ministerio_id')
                    ->label('Ministério')
                    ->relationship('ministerio', 'nome')
                    ->required(),

                Forms\Components\TextInput::make('titulo')
                    ->label('Título')
                    ->required(),

                Forms\Components\Textarea::make('descricao')
                    ->label('Descrição')
                    ->rows(4)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('local')
                    ->label('Local'),

                Forms\Components\DateTimePicker::make('data_inicio')
                    ->label('Data de Início')
                    ->required(),

                Forms\Components\DateTimePicker::make('data_fim')
                    ->label('Data de Término'),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'planejado' => 'Planejado',
                        'realizado' => 'Realizado',
                        'cancelado' => 'Cancelado',
                    ])
                    ->default('planejado')
                    ->required(),

                Forms\Components\TextInput::make('tipo_evento')
                    ->label('Tipo de Evento')
                    ->placeholder('Reunião, ensaio, culto...'),

                Forms\Components\Hidden::make('criado_por')
                    ->default(fn() => auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('titulo')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('ministerio.nome')->searchable()->label('Ministério')->sortable(),
                Tables\Columns\TextColumn::make('data_inicio')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('data_fim')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\BadgeColumn::make('status')->sortable()
                    ->colors([
                        'warning' => 'planejado',
                        'success' => 'realizado',
                        'danger' => 'cancelado',
                    ]),
                Tables\Columns\TextColumn::make('tipo_evento')->label('Tipo'),
                Tables\Columns\TextColumn::make('criador.name')->label('Criado por')->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d/m/Y H:i')->label('Criado em'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'planejado' => 'Planejado',
                        'realizado' => 'Realizado',
                        'cancelado' => 'Cancelado',
                    ]),
            ])
            ->defaultSort('data_inicio', 'desc')
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
            'index' => Pages\ListAgendas::route('/'),
            'create' => Pages\CreateAgenda::route('/create'),
            'edit' => Pages\EditAgenda::route('/{record}/edit'),
        ];
    }
}
