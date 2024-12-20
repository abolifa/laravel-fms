<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Tank;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Mutate and validate form data before saving the record.
     *
     * @param array $data
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $tankId = $data['tank_id'] ?? null;
        $newAmount = $data['amount'] ?? 0;
        $tank = Tank::find($tankId);

        if (!$tank) {
            Notification::make()
                ->title('Tank not found.')
                ->danger()
                ->send();
            throw ValidationException::withMessages([
                'tank_id' => 'Selected tank does not exist.',
            ]);
        }
        $originalAmount = $this->record->amount;
        $availableCapacity = $tank->capacity - ($tank->level - $originalAmount);

        if ($newAmount > $availableCapacity) {
            Notification::make()
                ->title('الكمية المطلوبة أكبر من الكمية المتاحة في الخزان')
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'amount' => 'The order amount exceeds the tank\'s available capacity.',
            ]);
        }
        return $data;
    }
}
