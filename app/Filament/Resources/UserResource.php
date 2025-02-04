<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Role;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\View\Components\Modal;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $getRole = Role::find(auth()->user()->role_id);

       /* if ($getRole->group_name == 'admin') {
            return static::getModel()::query()
                ->leftjoin('roles', 'users.role_id', 'roles.id');
        }else{
            return static::getModel()::query()
                ->leftjoin('roles','users.role_id','roles.id')
                ->where('roles.group_name',$getRole->group_name);
        }*/

        return static::getModel()::query()->whereHas('role', function ($q) use ($getRole) {
            if ($getRole->group_name != 'admin') {
                $q->where('group_name', $getRole->group_name);
            }
        });


    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                  Forms\Components\Grid::make(3) // Create a 2-column grid
                  ->schema([
                      Forms\Components\TextInput::make('name'),
                      Forms\Components\TextInput::make('email')
                          ->label('Email Address')
                          ->email()
                          ->required()
                          /*->afterStateUpdated(function ($state, callable $set) {
                              // Check if creating or editing, and apply unique rule accordingly

                          })*/
                          ->unique(ignoreRecord: true)
                    ->helperText('The email must be unique.'),


                Forms\Components\Select::make('role_id') // Use 'role_id' as the field name
                ->label('Role')
                    ->options(Role::pluck('name','id'))
//                    ->relationship('roles', 'name') // Assuming Role has a `name` field
                    ->required()
//                    ->searchable() // Optional: Makes the dropdown searchable
                    ->placeholder('Select a Role')

                  ]),
                 Forms\Components\Grid::make(3) // Create a 2-column grid
                 ->schema([
                     /*Forms\Components\TextInput::make('password')
                         ->label('Password')
                         ->password()
                         ->required()
                         ->minLength(8)
                         ->maxLength(255)
                         ->nullable(),  // Make it optional on update

                     Forms\Components\TextInput::make('confirmed')
                         ->label('Confirm Password')
                         ->password()
                         ->required()
                         ->minLength(8)
                         ->maxLength(255),*/
                     Forms\Components\TextInput::make('password')
                         ->password()
                         ->required(fn ($get) => ! $get('id')) // Only required if ID is not present (i.e., when creating)
                         ->maxLength(255)
                         ->hidden(fn ($get) => $get('id')) // Hide the field on edit
                         ->helperText('Leave blank if you do not want to change the password.'),


                 ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                Tables\Columns\TextColumn::make('role.name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                Tables\Columns\TextColumn::make('role.group_name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('update_password')
                    ->form([
                        Forms\Components\Grid::make(2) // Create a 2-column grid
                        ->schema([
                            Forms\Components\TextInput::make('password')
                                ->password()
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('confirm_password')
                                ->password()
                                ->required()
                                ->maxLength(255)

                        ])
                    ])
                    ->action(function (array $data, User $record): void {
                        $record->password = Hash::make($data['password']);
                        $record->save();

                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make()->visible(fn () => User::checkPermission(auth()->user()->role_id, 'delete-user')),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public function updatePassword($record)
    {
        $data = $this->form->getState();
        $record->update(['password' =>Hash::make($data['password'])]);
        session()->flash('success', 'Password updated successfully!');
    }

    public static function canViewAny(): bool
    {
        return User::checkPermission(auth()->user()->role_id, 'user-list');
    }
    public static function canCreate(): bool
    {
        return User::checkPermission(auth()->user()->role_id, 'create-user');
    }

    public static function canEdit(Model $record): bool
    {
        return User::checkPermission(auth()->user()->role_id, 'update-user');
    }

    public static function canDelete(Model $record): bool
    {
        return User::checkPermission(auth()->user()->role_id, 'delete-user');

    }
}
