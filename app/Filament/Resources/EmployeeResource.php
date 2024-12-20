<?php

namespace App\Filament\Resources;

use App\Enums\Major;
use App\Enums\Team;
use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers\CarsRelationManager;
use App\Models\Employee;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;
    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'heroicon-s-users';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make([
                    Section::make([
                        Forms\Components\TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->live()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->live()
                            ->unique(ignoreRecord: true)
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->live()
                            ->unique(ignoreRecord: true)
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address')
                            ->label('العنوان')
                            ->maxLength(255),
                        Forms\Components\Select::make('team')
                            ->label('الفريق')
                            ->options(Team::class)
                            ->default(Team::أخرى)
                            ->required(),
                        Forms\Components\Select::make('major')
                            ->label('التخصص')
                            ->options(Major::class)
                            ->default(Major::موظف)
                            ->required(),

                    ]),
                ]),
                Group::make([
                    Section::make([
                        SpatieMediaLibraryFileUpload::make('avatar')
                            ->label('الصورة الشخصية')
                            ->avatar()
                            ->collection('avatars')
                            ->imageEditor()
                            ->alignCenter()
                            ->grow(false),
                        Group::make([
                            Forms\Components\DatePicker::make('hiring_date')->default(now()),
                            Forms\Components\DatePicker::make('leaving_date')->default(Carbon::now()->addDecade()),
                        ])->columns(2),
                        Forms\Components\TextInput::make('quota')
                            ->label('الحصة الشهرية')
                            ->required()
                            ->numeric()
                            ->default(200),
                        Forms\Components\TextInput::make('original_quota')
                            ->label('الحصة الشهرية الأصلية')
                            ->required()
                            ->numeric()
                            ->default(200),

                        Forms\Components\ToggleButtons::make('is_active')
                            ->label('نشط')
                            ->boolean()
                            ->options([
                                true => 'نعم',
                                false => 'لا',
                            ])
                            ->default(true)
                            ->inline()
                            ->grouped()
                            ->required(),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('avatar')
                    ->label('')
                    ->circular()
                    ->collection('avatars'),
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),
                Tables\Columns\TextColumn::make('team')
                    ->formatStateUsing(fn($state) => Team::tryFrom($state)?->name ?? $state)
                    ->badge()
                    ->label('الفريق'),
                Tables\Columns\TextColumn::make('major')
                    ->formatStateUsing(fn($state) => Major::tryFrom($state)?->name ?? $state)
                    ->badge()
                    ->color(fn($state) => match (Major::tryFrom($state)) {
                        Major::موظف => 'gray',
                        Major::مدير => 'danger',
                        Major::مشرف => 'warning',
                        Major::مشغل => 'danger',
                        Major::تسليح => 'danger',
                        Major::إلكتروني => 'info',
                        Major::ميكانيكي => 'warning',
                        Major::كاميرا => 'danger',
                        default => 'gray',
                    })
                    ->label('التخصص'),
                Tables\Columns\TextColumn::make('quota')
                    ->label('الحصة الشهرية')
                    ->badge()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('نشط'),
                Tables\Columns\TextColumn::make('hiring_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('leaving_date')
                    ->date()
                    ->sortable()
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            CarsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return 'موظف';
    }

    public static function getPluralLabel(): ?string
    {
        return 'الموظفين';
    }
}
