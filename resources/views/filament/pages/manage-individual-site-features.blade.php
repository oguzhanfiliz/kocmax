<x-filament-panels::page>
    {{ $this->form }}
    
    <x-filament-actions::group :actions="$this->getFormActions()" />
</x-filament-panels::page>