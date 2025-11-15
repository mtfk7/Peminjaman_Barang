<x-filament::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @livewire(\App\Filament\Widgets\StatsOverview::class)
        @livewire(\App\Filament\Widgets\BarangPerluRestockTable::class)
    </div>
</x-filament::page>
