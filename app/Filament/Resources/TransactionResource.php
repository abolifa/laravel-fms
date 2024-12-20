<?php

namespace App\Filament\Resources;


use App\Enums\TransactionStatus;
use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Fuel;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Nette\Utils\Random;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'maki-fuel';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make([
                    Section::make([
                        Forms\Components\Select::make('tank_id')
                            ->label('الخزان')
                            ->required()
                            ->relationship('tank', 'name'),
                        Forms\Components\Select::make('employee_id')
                            ->label('الموظف')
                            ->required()
                            ->reactive()
                            ->relationship('employee', 'name'),
                        Forms\Components\Select::make('car_id')
                            ->label('السيارة')
                            ->required()
                            ->options(function (callable $get) {
                                $employeeId = $get('employee_id');
                                if (!$employeeId) {
                                    return [];
                                }
                                return \App\Models\Car::where('employee_id', $employeeId)
                                    ->pluck('model', 'id');
                            })
                            ->reactive(),
                        Forms\Components\TextInput::make('amount')
                            ->label('الكمية')
                            ->numeric()
                            ->disabled(fn(callable $get) => $get('status') !== 'مكتمل')
                            ->required(fn(callable $get) => $get('status') === 'مكتمل')
                            ->rules([
                                'required_if:status,مكتمل',
                                'nullable',
                                'numeric',
                                'min:0',
                            ])
                            ->helperText('لوضع كمية للعملية٫ يجب تغيير الحالة إلي مكتمل'),
                    ]),
                ]),

                Group::make([
                    Section::make([
                        Forms\Components\TextInput::make('number')
                            ->label('رقم الطلب')
                            ->default(Random::generate('5', '0-9')),
                        Forms\Components\ToggleButtons::make('status')
                            ->label('الحالة')
                            ->live()
                            ->options([
                                'معلق' => 'معلق',
                                'مكتمل' => 'مكتمل',
                                'ملغي' => 'ملغي',
                            ])
                            ->default('معلق')
                            ->inline()
                            ->grouped(),
                    ])
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('رقم الطلب')
                    ->prefix('#')
                    ->badge(),
                Tables\Columns\TextColumn::make('tank.name')
                    ->label('الخزان')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('الموظف')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('car.model')
                    ->label('السيارة')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('الكمية')
                    ->suffix(' لتر ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('حالة العملية'),
                Tables\Columns\TextColumn::make('created_by')
                    ->label('أنشئ بواسطة')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('approved_by')
                    ->label('تمت الموافقة بواسطة')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Action::make('print')
                    ->label('طباعة الإيصال')
                    ->icon('heroicon-s-printer')
                    ->url(fn(Transaction $record) => route('transaction.print', ['transaction' => $record]))
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return 'عملية';
    }

    public static function getPluralLabel(): ?string
    {
        return 'عمليات التعبئة';
    }
}
