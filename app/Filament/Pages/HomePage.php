<?php

namespace App\Filament\Pages;

use App\Models\Tank;
use Filament\Pages\Page;

class HomePage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.home-page';
    protected static ?string $title = 'الرئيسية';

    public function getTanks()
    {
        return Tank::all();
    }
}
