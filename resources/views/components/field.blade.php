@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null])
<div class="field">
    <label for="{{ $name }}">{{ $label }}</label>
    @if ($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" {{ $attributes->class('input')->merge(['rows' => 4]) }}
            aria-describedby="{{ $name }}-help" @error($name) aria-invalid="true" @enderror>{{ old($name, $value) }}</textarea>
    @elseif ($type === 'select')
        <select id="{{ $name }}" name="{{ $name }}" {{ $attributes->class('input') }}
            aria-describedby="{{ $name }}-help"
            @error($name) aria-invalid="true" @enderror>{{ $slot }}</select>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
            @if (!in_array($type, ['password', 'file'])) value="{{ old($name, $value) }}" @endif
            {{ $attributes->class('input') }} aria-describedby="{{ $name }}-help"
            @error($name) aria-invalid="true" @enderror>
    @endif
    <div id="{{ $name }}-help">
        @error($name)<p class="field-error">{{ $message }}</p>
        @else
            @if ($hint)
                <p class="field-hint">{{ $hint }}</p>
            @endif
        @enderror
    </div>
</div>
