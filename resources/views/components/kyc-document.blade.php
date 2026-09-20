@props(['verification', 'type', 'label'])
@php
    $url = route('kyc.documents.show', [$verification, $type]);
    $path = $type === 'id-card' ? $verification->id_card_image : $verification->fee_receipt_image;
    $isImage = in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']);
    $modalId = 'document-' . $verification->id . '-' . $type;
@endphp
<div class="stack gap-3">
    <h3 class="text-sm">{{ $label }}</h3>
    @if ($isImage)
        <button type="button" data-dialog="{{ $modalId }}" aria-label="Enlarge {{ $label }}">
            <img class="document-preview" src="{{ $url }}" alt="Submitted {{ strtolower($label) }}" loading="lazy">
        </button>
        <x-modal :id="$modalId" :title="$label" class="max-w-4xl">
            <img src="{{ $url }}" alt="Submitted {{ strtolower($label) }}"
                class="w-full max-h-[65vh] object-contain">
            <a href="{{ $url }}" target="_blank" rel="noopener" class="text-link mt-4">Open original document
                <x-icon name="arrow" size="16" />
            </a>
        </x-modal>
    @else
        <div class="document-pdf">
            <x-icon name="file" size="40" />
            <p class="text-xs">Private document</p>
            <a class="btn btn-secondary" href="{{ $url }}" target="_blank" rel="noopener">View PDF / document
                <x-icon name="arrow" size="15" />
            </a>
        </div>
    @endif
    <p class="field-hint">
        <x-icon name="lock" size="12" /> Accessible only to the student and authorized
        administrators.
    </p>
</div>
