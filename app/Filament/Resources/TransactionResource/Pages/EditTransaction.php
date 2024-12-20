<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Car;
use App\Models\Employee;
use App\Models\Tank;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

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
        $employeeId = $data['employee_id'] ?? null;
        $carId = $data['car_id'] ?? null;
        $fuelId = $data['fuel_id'] ?? null;
        $newAmount = $data['amount'] ?? 0;

        $tank = Tank::find($tankId);
        $employee = Employee::find($employeeId);
        $car = Car::find($carId);

        if (!$tank || !$employee || !$car) {
            Notification::make()
                ->title('Invalid Data')
                ->danger()
                ->send();
            throw ValidationException::withMessages([
                'tank_id' => 'Invalid tank, employee, or car.',
            ]);
        }

        // Check if the tank and car have matching fuel types
        if ($tank->fuel_id != $car->fuel_id) {
            Notification::make()
                ->title('نوع الوقود في الخزان لا يتطابق مع نوع الوقود في السيارة')
                ->danger()
                ->send();
            throw ValidationException::withMessages([
                'tank_id' => 'The fuel type in the tank does not match the fuel type in the car.',
            ]);
        }

        // Handle tank and employee capacity validation considering the current record
        $originalAmount = $this->record->amount;
        $availableTankLevel = $tank->level + $originalAmount; // Restore original amount to tank
        $availableQuota = $employee->quota + $originalAmount; // Restore original amount to employee quota

        // Warn if the new amount exceeds the employee quota
        if ($newAmount > $availableQuota) {
            Notification::make()
                ->title('تحذير: الحصة الشهرية للموظف تم تجاوزها')
                ->body('تمت الموافقة على المعاملة ولكن الكمية المطلوبة تتجاوز الحصة الشهرية للموظف.')
                ->warning()
                ->send();
        }

        // Prevent if the new amount exceeds the tank level
        if ($newAmount > $availableTankLevel) {
            Notification::make()
                ->title('الكمية تتجاوز الكمية المتاحة في الخزان')
                ->danger()
                ->send();
            throw ValidationException::withMessages([
                'amount' => 'The transaction amount exceeds the available tank level.',
            ]);
        }

        return $data;
    }
}
