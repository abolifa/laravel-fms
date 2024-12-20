<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TankResource\Pages;
use App\Models\Tank;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class TankResource extends Resource
{
    protected static ?string $model = Tank::class;

    protected static ?string $navigationIcon = 'mdi-tanker-truck';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('fuel_id')
                    ->label('الوقود')
                    ->relationship('fuel', 'type'),
                Forms\Components\TextInput::make('name')
                    ->label('اسم الخزان')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('capacity')
                    ->label('السعة')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('level')
                    ->label('المستوى الحالي')
                    ->required()
                    ->numeric(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fuel.type')
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
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الخزان')
                    ->searchable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->label('السعة')
                    ->suffix(' لتر ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('level')
                    ->label('المستوى')
                    ->badge()
                    ->color(
                        fn($record) => ($record->level / $record->capacity) < 0.1
                            ? 'danger'
                            : (($record->level / $record->capacity) <= 0.5
                                ? 'warning'
                                : 'success')
                    )
                    ->suffix(' لتر ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاريخ التعديل')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label('طباعة تقرير')
                    ->icon('heroicon-s-printer')
                    ->url(fn(Tank $record) => route('tank.print', ['tank' => $record]))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListTanks::route('/'),
        ];
    }

    public static function getLabel(): ?string
    {
        return 'خزان';
    }

    public static function getPluralLabel(): ?string
    {
        return 'الخزانات';
    }
}
