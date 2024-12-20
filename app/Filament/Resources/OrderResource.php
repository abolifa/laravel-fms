<?php

namespace App\Filament\Resources;

use App\Enums\FuelType;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'mdi-receipt';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Forms\Components\TextInput::make('supplier')
                        ->label('المورد')
                        ->maxLength(255),
                    Forms\Components\Select::make('tank_id')
                        ->label('الخزان')
                        ->required()
                        ->relationship('tank', 'name'),
                    Forms\Components\Select::make('fuel_id')
                        ->label('الوقود')
                        ->required()
                        ->relationship('fuel', 'type'),
                    Forms\Components\TextInput::make('amount')
                        ->label('الكمية')
                        ->numeric()
                        ->required(),
                    Forms\Components\DatePicker::make('order_date')
                        ->label('تاريخ الطلب')
                        ->default(now()),
                ])->maxWidth('2xl'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('supplier')
                    ->label('المورد')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tank.name')
                    ->label('الخزان')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fuel.type')
                    ->badge()
                    ->label('الوقود')
                    ->badge()
                    ->color(function ($record) {
                        $colors = [
                            'بنزين' => 'success',
                            'ديزل' => 'warning',
                            'قاز' => 'danger',
                        ];

                        return $colors[$record->fuel->type] ?? 'secondary';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->suffix(' لتر ')
                    ->label('الكمية'),
                Tables\Columns\TextColumn::make('order_date')
                    ->label('تاريخ الطلب')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('تاريخ التعديل')
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return 'طلب وقود';
    }

    public static function getPluralLabel(): ?string
    {
        return 'طلبات الوقود';
    }
}
