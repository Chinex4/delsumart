@props(['label', 'value', 'icon' => 'grid', 'hint' => null])
<div class="stat-card">
    <div class="stat-top">
        <span>{{ $label }}</span>
        <span class="stat-icon">
            <x-icon :name="$icon" />
        </span>
    </div>
    <p class="stat-value">{{ is_numeric($value) ? number_format($value) : $value }}</p>
    @if ($hint)
        <p class="field-hint">{{ $hint }}</p>
    @endif
</div>
