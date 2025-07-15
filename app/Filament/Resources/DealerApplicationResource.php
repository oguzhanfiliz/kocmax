<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DealerApplicationResource\Pages;
use App\Filament\Resources\DealerApplicationResource\RelationManagers;
use App\Models\DealerApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DealerApplicationResource extends Resource
{
    protected static ?string $model = DealerApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Bayi Yönetimi';

    public static function getPluralModelLabel(): string
    {
        return __('Bayi Başvuruları');
    }

    public static function getModelLabel(): string
    {
        return __('Bayi Başvurusu');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('company_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('tax_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('trade_registry_document_path')
                    ->required(),
                Forms\Components\FileUpload::make('tax_plate_document_path')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->searchable(),
                Tables\Columns\TextColumn::make('company_name')->searchable(),
                Tables\Columns\TextColumn::make('tax_number')->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->action(function (DealerApplication $record) {
                        $record->update(['status' => 'approved']);
                        $record->user->update(['is_approved_dealer' => true]);
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->action(function (DealerApplication $record) {
                        $record->update(['status' => 'rejected']);
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDealerApplications::route('/'),
        ];
    }
}
