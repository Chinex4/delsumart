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
        <div @if ($type === 'password') class="password-field" @endif>
            <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
                @if (!in_array($type, ['password', 'file'])) value="{{ old($name, $value) }}" @endif
                {{ $attributes->class('input') }} aria-describedby="{{ $name }}-help"
                @error($name) aria-invalid="true" @enderror>
            @if ($type === 'password')
                <button type="button" class="password-toggle" data-password-toggle="{{ $name }}"
                    aria-label="Show password" aria-pressed="false">
                    <svg class="password-eye password-eye-open" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" />
                        <circle cx="12" cy="12" r="2.5" />
                    </svg>
                    <svg class="password-eye password-eye-closed" viewBox="0 0 24 24" aria-hidden="true" hidden>
                        <path d="m3 3 18 18M10.6 6.2A10.6 10.6 0 0 1 12 6c6 0 9.5 6 9.5 6a17 17 0 0 1-3 3.7M6.2 6.2C3.8 8 2.5 12 2.5 12s3.5 6 9.5 6c1.5 0 2.8-.4 4-1" />
                    </svg>
                </button>
            @endif
        </div>
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
