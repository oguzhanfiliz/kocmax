<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <x-filament::input.wrapper
        :disabled="$isDisabled()"
        :inline-suffix="$getInlineSuffix()"
        :prefix="$getPrefix()"
        :prefix-icon="$getPrefixIcon()"
        :suffix="$getSuffix()"
        :suffix-icon="$getSuffixIcon()"
    >
        <x-filament::select
            :attributes="$getExtraInputAttributeBag()->class(['fi-select-input'])"
            :disabled="$isDisabled()"
            :id="$getId()"
            :inline-suffix="$getInlineSuffix()"
            :prefix="$getPrefix()"
            :prefix-icon="$getPrefixIcon()"
            :suffix="$getSuffix()"
            :suffix-icon="$getSuffixIcon()"
            :type="$getType()"
            :value="$getState()"
            :wire:model.live.debounce.500ms="$getLivewireKey()"
        >
            <option value="">{{ $getPlaceholder() }}</option>
            
            @foreach ($getOptions() as $value => $label)
                <option value="{{ $value }}" @selected($getState() == $value)>
                    {{ $label }}
                </option>
            @endforeach
        </x-filament::select>
    </x-filament::input.wrapper>
</x-dynamic-component>
