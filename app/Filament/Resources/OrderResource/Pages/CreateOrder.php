<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Tank;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    /**
     * Mutate and validate form data before creating the record.
     *
     * @param array $data
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract 'tank_id' and 'amount' from form data
        $tankId = $data['tank_id'] ?? null;
        $amount = $data['amount'] ?? 0;

        // Fetch the Tank model
        $tank = Tank::find($tankId);

        if (!$tank) {
            // Send a danger notification
            Notification::make()
                ->title('Tank not found.')
                ->danger()
                ->send();

            // Throw a validation exception to halt the creation
            throw ValidationException::withMessages([
                'tank_id' => 'Selected tank does not exist.',
            ]);
        }

        // Calculate available capacity
        $availableCapacity = $tank->capacity - $tank->level;

        if ($amount > $availableCapacity) {
            // Send a danger notification
            Notification::make()
                ->title('الكمية المطلوبة أكبر من الكمية المتاحة في الخزان') // "The requested quantity exceeds the available tank capacity"
                ->danger()
                ->send();

            // Throw a validation exception to halt the creation
            throw ValidationException::withMessages([
                'amount' => 'The order amount exceeds the tank\'s available capacity.',
            ]);
        }

        // If validation passes, return the data unchanged
        return $data;
    }
}
