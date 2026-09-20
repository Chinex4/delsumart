@props(['id', 'title'])
<dialog id="{{ $id }}" {{ $attributes->class('modal') }} aria-labelledby="{{ $id }}-title">
    <div class="modal-heading">
        <h2 id="{{ $id }}-title">{{ $title }}</h2>
        <button type="button" class="icon-button" data-close-dialog aria-label="Close dialog">
            <x-icon name="close" />
        </button>
    </div>
    <div class="modal-body">{{ $slot }}</div>
</dialog>
