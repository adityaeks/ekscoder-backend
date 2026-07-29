@props([
    'id',
    'title' => '',
    'maxWidth' => '580px',
    'closeOnBackdrop' => false
])

<div class="modal-backdrop" id="{{ $id }}" onclick="handleAdminModalBackdropClick(event, '{{ $id }}', {{ $closeOnBackdrop ? 'true' : 'false' }})">
    <div class="modal-box" style="max-width: {{ $maxWidth }};">
        <div class="modal-header">
            <div class="modal-title">{{ $title }}</div>
            <button type="button" class="modal-close" onclick="closeModal('{{ $id }}')">&times;</button>
        </div>
        
        {{ $slot }}
    </div>
</div>
