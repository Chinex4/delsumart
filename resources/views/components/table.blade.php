@props(['label'])
<div class="table-scroll" role="region" aria-label="{{ $label }}" tabindex="0">
    <table class="data-table">
        <caption class="sr-only">{{ $label }}</caption>{{ $slot }}
    </table>
</div>
