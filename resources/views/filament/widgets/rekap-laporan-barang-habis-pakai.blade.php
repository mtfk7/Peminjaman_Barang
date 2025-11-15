<x-filament-widgets::widget>
    <x-filament::section>
        <form wire:submit.prevent="save">
            {{ $this->form }}
        </form>

        @php
            $rekap = $this->getRekap();
        @endphp

        <div class="mt-4 grid grid-cols-2 gap-4">
            <div class="p-4 bg-gray-800 text-white rounded-lg">
                <h4 class="text-sm">Total Transaksi</h4>
                <p class="text-2xl font-bold">{{ $rekap['total_transaksi'] }}</p>
            </div>

            <div class="p-4 bg-gray-800 text-white rounded-lg">
                <h4 class="text-sm">Total Barang Dipinjam</h4>
                <p class="text-2xl font-bold">{{ $rekap['total_dipinjam'] }}</p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
