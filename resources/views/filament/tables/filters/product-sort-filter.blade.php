<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div class="space-y-3">
        <x-filament::input.wrapper>
            <x-filament::select
                :attributes="$getExtraInputAttributeBag()->class(['fi-select-input'])"
                :disabled="$isDisabled()"
                :id="$getId()"
                :value="$getState()"
                :wire:model.live.debounce.500ms="$getLivewireKey()"
            >
                @foreach ($getOptions() as $value => $label)
                    <option value="{{ $value }}" @selected($getState() == $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </x-filament::select>
        </x-filament::input.wrapper>
    </div>
</x-dynamic-component>
