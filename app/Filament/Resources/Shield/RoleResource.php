<?php

namespace App\Filament\Resources\Shield;

use App\Filament\Resources\Shield\RoleResource\Pages;
use App\Filament\Resources\Shield\RoleResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    
    protected static ?string $navigationGroup = 'Sistem';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $slug = 'shield/roles';

    public static function getPluralModelLabel(): string
    {
        return __('Roller');
    }

    public static function getModelLabel(): string
    {
        return __('Rol');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Rol Adı')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\Select::make('permissions')
                            ->label('İzinler')
                            ->multiple()
                            ->relationship('permissions', 'name')
                            ->preload()
                            ->searchable(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Rol Adı')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('İzin Sayısı')
                    ->counts('permissions')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Kullanıcı Sayısı')
                    ->counts('users')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellenme Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Role $record) {
                        if ($record->name === 'admin' || $record->name === 'super_admin') {
                            return false;
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Tables\Actions\BulkAction $action) {
                            $action->cancel();
                            
                            foreach ($action->getRecords() as $record) {
                                if ($record->name !== 'admin' && $record->name !== 'super_admin') {
                                    $record->delete();
                                }
                            }
                        }),
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
}
