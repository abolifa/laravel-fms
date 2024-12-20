<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Car;
use App\Models\Employee;
use App\Models\Tank;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    /**
     * Mutate and validate form data before creating the record.
     *
     * @param array $data
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tankId = $data['tank_id'] ?? null;
        $employeeId = $data['employee_id'] ?? null;
        $carId = $data['car_id'] ?? null;
        $fuelId = $data['fuel_id'] ?? null;
        $amount = $data['amount'] ?? 0;

        $tank = Tank::find($tankId);
        $employee = Employee::find($employeeId);
        $car = Car::find($carId);

        if ($tank->fuel_id != $car->fuel_id) {
            Notification::make()
                ->title('نوع الوقود في الخزان لا يتطابق مع نوع الوقود في السيارة')
                ->danger()
                ->send();
            throw ValidationException::withMessages([
                'tank_id' => 'The fuel type in the tank does not match the fuel type in the car.',
            ]);
        }


        // Warn if employee quota is exceeded
        if ($amount > $employee->quota) {
            Notification::make()
                ->title('تحذير: الحصة الشهرية للموظف تم تجاوزها')
                ->body('تمت الموافقة على المعاملة ولكن الكمية المطلوبة تتجاوز الحصة الشهرية للموظف.')
                ->warning()
                ->send();
        }

        if ($amount > $tank->level) {
            Notification::make()
                ->title('الكمية تتجاوز الكمية المتاحة في الخزان')
                ->danger()
                ->send();
            throw ValidationException::withMessages([
                'amount' => 'The order amount exceeds the tank\'s available capacity.',
            ]);
        }
        return $data;
    }
}
