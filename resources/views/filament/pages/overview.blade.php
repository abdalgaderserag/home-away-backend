<x-filament-panels::page>
    {{-- This will render all the widgets defined in your PulseOverview page class --}}
    {{ $this->form }} {{-- $this->form is usually for pages with forms, you might not need this here if it's just widgets --}}

    {{-- You can customize the grid layout here if needed --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach ($this->getHeaderWidgets() as $widget)
            @livewire($widget)
        @endforeach
        {{-- If you have footer widgets, you'd iterate over getFooterWidgets() like this: --}}
        {{-- @foreach ($this->getFooterWidgets() as $widget)
            @livewire($widget)
        @endforeach --}}
    </div>

    {{-- Remove the problematic line: --}}
    {{-- {{ $this->hasFooterWidgets() ? $this->getFooterWidgets() : null }} --}}

</x-filament-panels::page>