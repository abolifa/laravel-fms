<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceResource\Pages;
use App\Models\Maintenance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MaintenanceResource extends Resource
{
    protected static ?string $model = Maintenance::class;
    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'mdi-hammer-wrench';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('car_id')
                    ->label('المركبة')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->relationship('car', 'model', function ($query) {
                        $query->where('is_eaa', true);
                    }),
                Forms\Components\TextInput::make('mileage')
                    ->required()
                    ->label('العداد'),
                Forms\Components\Select::make('maintenance_types')
                    ->label('أنواع الصيانة')
                    ->preload()
                    ->searchable()
                    ->relationship('maintenanceTypes', 'name')
                    ->multiple(),
                Forms\Components\Textarea::make('description')
                    ->label('الوصف')
                    ->maxLength(255),
                Forms\Components\TextInput::make('cost')
                    ->label('التكلفة')
                    ->numeric()
                    ->prefix('$'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('car.model')
                    ->label('المركبة')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mileage')
                    ->label('العداد')
                    ->numeric()
                    ->suffix(' km ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('maintenanceTypes.name')
                    ->label('أنواع الصيانة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('الوصف')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('cost')
                    ->label('التكلفة')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاريخ التعديل')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListMaintenances::route('/'),
        ];
    }

    public static function getLabel(): ?string
    {
        return 'صيانة';
    }

    public static function getPluralLabel(): ?string
    {
        return 'الصيانة';
    }
}
