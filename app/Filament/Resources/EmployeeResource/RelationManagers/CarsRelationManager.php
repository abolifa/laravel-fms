<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;


class CarsRelationManager extends RelationManager
{
    protected static string $relationship = 'cars';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\ToggleButtons::make('is_eaa')
                    ->label("ملكية الجهاز")
                    ->options([
                        true => 'الجهاز',
                        false => 'الموظف',
                    ])->inline()->grouped()->default(false),
                Forms\Components\TextInput::make('model')
                    ->label('نوع المركبة')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('plate')
                    ->label('رقم اللوحة')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('fuel_id')
                    ->label('نوع الوقود')
                    ->required()
                    ->relationship('fuel', 'type'),
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modelLabel('مركبة')
            ->pluralModelLabel('المركبات')
            ->columns([
                Tables\Columns\TextColumn::make('model')
                    ->label('نوع المركبة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('plate')
                    ->label('رقم اللوحة')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('fuel.type')
                    ->label('نوع الوقود')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_eaa')
                    ->label('ملكية الجهاز')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
