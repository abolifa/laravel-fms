<x-filament::page>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-4">
        @foreach ($this->getTanks() as $tank)
        @php
        $levelPercentage = $tank->capacity > 0 ? round(($tank->level / $tank->capacity) * 100, 2) : 0;
        $levelColor = $levelPercentage < 30 ? 'bg-red-500' : ($levelPercentage < 70 ? 'bg-yellow-500' : 'bg-green-500' );
            $fuelColor=$tank->fuel->type === 'ديزل' ? 'bg-amber-500' : ($tank->fuel->type === 'بنزين' ? 'bg-teal-500' : 'bg-rose-500');
            @endphp

            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6 border border-gray-200 dark:border-gray-700 w-full h-full flex flex-col items-start gap-4">
                <!-- Tank Title -->
                <div class="flex flex-row items-center justify-between w-full">
                    <h3 class="text-lg font-bold text-black dark:text-white">{{ $tank->name }}</h3>
                    <span class="text-sm px-3 py-1 bg-amber-500 rounded-lg text-black"> {{ $tank->fuel->type }}</span>
                </div>

                <!-- Tank Capacity -->
                <p class="text-sm text-gray-600 dark:text-gray-400">Capacity: {{ $tank->capacity }} L</p>

                <!-- Progress Bar -->
                <div class="w-full mt-4">
                    <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full {{ $levelColor }} transition-all duration-300" style="width: {{ $levelPercentage }}%;"></div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 text-right">
                        {{ $levelPercentage }}% Full
                    </p>
                </div>

                <!-- Status Indicator -->
                <div class="flex items-center gap-2 mt-4">
                    <div class="w-3 h-3 rounded-full {{ $levelColor }}"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300">
                        {{ $levelPercentage < 30 ? 'Low Level' : ($levelPercentage < 70 ? 'Moderate Level' : 'High Level') }}
                    </span>
                </div>
            </div>
            @endforeach
    </div>
</x-filament::page>