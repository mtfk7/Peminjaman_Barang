<x-filament-panels::page.simple>
    <x-slot name="title">
        Login
    </x-slot>

    <x-filament::form wire:submit.prevent="authenticate">
        {{ $this->form }}

        <x-filament::button type="submit" class="w-full mt-4">
            Masuk
        </x-filament::button>
    </x-filament::form>
</x-filament-panels::page.simple>
