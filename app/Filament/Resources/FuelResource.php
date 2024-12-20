<?php

namespace App\Filament\Resources;

use App\Enums\FuelType;
use App\Filament\Resources\FuelResource\Pages;
use App\Filament\Resources\FuelResource\RelationManagers;
use App\Models\Fuel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FuelResource extends Resource
{
    protected static ?string $model = Fuel::class;
    protected static ?int $navigationSort = 6;

    protected static ?string $navigationIcon = 'mdi-fuel';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('type')
                    ->label('نوع الوقود')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('نوع الوقود')
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->color(function ($record) {
                        $colors = [
                            'بنزين' => 'success',
                            'ديزل' => 'warning',
                            'قاز' => 'danger',
                        ];

                        return $colors[$record->type] ?? 'secondary';
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
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
            'index' => Pages\ListFuels::route('/'),
        ];
    }

    public static function getLabel(): ?string
    {
        return 'وقود';
    }

    public static function getPluralLabel(): ?string
    {
        return 'أنواع الوقود';
    }
}
