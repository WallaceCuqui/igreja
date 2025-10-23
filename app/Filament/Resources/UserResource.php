<?php

namespace App\Filament\Resources;

use Illuminate\Support\Facades\Log;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;

use App\Rules\DocumentoValido;

use Illuminate\Validation\Rule;
use Filament\Resources\Resource;
use Filament\Forms\Get;
use Filament\Forms\Set;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

//Para verificar as permissões
use App\Filament\Resources\Traits\HasModuleAccess;

class UserResource extends Resource
{
    

    // só define qual módulo será usado
    // o nome do módulo deve ser igual ao definido em config/modules.php
    use HasModuleAccess;
    protected static string $moduleForAccess = 'users';

    protected static ?string $navigationLabel = 'Usuários';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Administração';


    public static function form(Form $form): Form
    {
        $user = auth()->user();
        /*\Log::info('🔹 Usuário logado no UserResource', [
            'id' => $user?->id,
            'email' => $user?->email,
            'roles' => $user?->roles?->pluck('name')->toArray(),
        ]);*/

        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Nome'),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label('E-mail'),

                TextInput::make('password')
                    ->password()
                    ->required(fn ($record) => !$record)
                    ->label('Senha')
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state)),

                Select::make('roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->label('Grupo Acesso')
                    ->preload(),

                Section::make('Dados do Usuário')
                    ->relationship('detalhesUsuario')
                    ->schema([
                        TextInput::make('nome_fantasia')->label('Nome Fantasia'),
                        TextInput::make('documento')
                            ->label('CPF/CNPJ')
                            ->live(onBlur: true)
                            ->rule(function ($get, $record) {
                                $userId = $record?->id; // pega o id do usuário principal
                                return Rule::unique('detalhes_usuario', 'documento')->ignore($userId, 'user_id');
                            })
                            ->rule(new DocumentoValido()),
                        Select::make('genero')
                            ->label('Gênero')
                            ->options([
                                'Masculino' => 'Masculino',
                                'Feminino' => 'Feminino',
                                'Outro' => 'Outro',
                            ]),
                        DatePicker::make('data_nascimento')->label('Data de Nascimento'),

                        TextInput::make('cep')
                            ->label('CEP')
                            ->mask('99999-999')
                            ->live(debounce: 500)
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $cep = preg_replace('/\D/', '', $state);

                                if (strlen($cep) === 8) {
                                    $set('buscando_cep', true); // ativa aviso

                                    try {
                                        $response = @file_get_contents("https://viacep.com.br/ws/{$cep}/json/");
                                        if ($response) {
                                            $data = json_decode($response, true);
                                            if (!isset($data['erro'])) {
                                                $set('endereco', $data['logradouro'] ?? '');
                                                $set('bairro', $data['bairro'] ?? '');
                                                $set('cidade', $data['localidade'] ?? '');
                                                $set('estado', $data['uf'] ?? '');
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        // opcional: mensagem de erro
                                    }

                                    $set('buscando_cep', false); // desativa aviso
                                } else {
                                    $set('buscando_cep', false);
                                }
                            }),

                        TextInput::make('endereco')->label('Endereço'),
                        TextInput::make('numero')->label('Número'),
                        TextInput::make('complemento')->label('Complemento'),
                        TextInput::make('bairro')->label('Bairro'),
                        TextInput::make('cidade')->label('Cidade'),
                        TextInput::make('estado')->label('Estado'),
                        
                        TextInput::make('telefone')->label('Telefone'),
                    ]),
                
            ])
            ->columns(2);
        
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();
        /*\Log::info('🔹 Usuário logado no UserResource', [
            'id' => $user?->id,
            'email' => $user?->email,
            'roles' => $user?->roles?->pluck('name')->toArray(),
        ]);*/

        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('roles.name')
                    ->label('Papéis')
                    ->sortable()
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
        $user = auth()->user();
        /*\Log::info('🔹 Usuário logado no UserResource', [
            'id' => $user?->id,
            'email' => $user?->email,
            'roles' => $user?->roles?->pluck('name')->toArray(),
        ]);*/

        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
