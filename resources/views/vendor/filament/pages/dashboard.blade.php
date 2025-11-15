<x-filament-panels::page>
    {{-- Dashboard custom kamu tanpa card default --}}
    <x-filament-widgets::widgets
        :widgets="$this->getWidgets()"
        :columns="$this->getColumns()"
    />
</x-filament-panels::page>
