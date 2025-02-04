<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Role;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2) // Create a 2-column grid
                ->schema([

                    Forms\Components\TextInput::make('name'),
                    Forms\Components\TextInput::make('group_name'),
                ]),
                Forms\Components\Grid::make(1) // Create a 2-column grid
                ->schema([
                    Forms\Components\CheckboxList::make('permissions')
                        ->relationship('permissions', 'name') // Relationship to permissions
                        ->columns(2) // Adjust columns for layout (optional)
                        ->helperText('Select the permissions to assign to this role.')
                        ->required(),
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
                Tables\Columns\TextColumn::make('group_name')
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => User::checkPermission(auth()->user()->role_id, 'delete-role')),

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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        return User::checkPermission(auth()->user()->role_id, 'role-list');
    }
    public static function canCreate(): bool
    {
        return User::checkPermission(auth()->user()->role_id, 'create-role');
    }

    public static function canEdit(Model $record): bool
    {
        return User::checkPermission(auth()->user()->role_id, 'update-role');
    }

    public static function canDelete(Model $record): bool
    {
        return User::checkPermission(auth()->user()->role_id, 'delete-role');
    }
}
