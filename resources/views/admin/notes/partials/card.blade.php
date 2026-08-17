@php
    $style = $note->color_style;
    $authorName = $note->user ? ($note->user->id === auth()->id() ? 'Anda' : $note->user->name) : 'Anonim';
@endphp

<div class="note-card" data-id="{{ $note->id }}" style="background: {{ $style['bg'] }}; border: 1px solid {{ $style['border'] }}; cursor: grab;" onclick="openEditModal({{ $note->id }}, {{ json_encode($note->title) }}, {{ json_encode($note->content) }}, {{ json_encode($note->color) }}, {{ $note->is_pinned ? 'true' : 'false' }}, {{ json_encode($authorName) }})">

    <div>
        <!-- Card Header: Title & Pin -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; gap: 8px;">
            @if($note->title)
                <h4 style="margin: 0; font-size: 15px; font-weight: 700; color: var(--text-primary); line-height: 1.4;">
                    {{ $note->title }}
                </h4>
            @else
                <div></div>
            @endif

            <form action="{{ route('admin.notes.pin', $note->id) }}" method="POST" style="margin: 0;" onclick="event.stopPropagation();">
                @csrf
                <button type="submit" style="background: none; border: none; cursor: pointer; font-size: 15px; padding: 2px; opacity: {{ $note->is_pinned ? '1' : '0.4' }}; transition: opacity 0.2s;" title="{{ $note->is_pinned ? 'Lepas sematan' : 'Sematkan catatan' }}">
                    📌
                </button>
            </form>
        </div>

        <!-- Card Content -->
        @if($note->content)
            <div style="font-size: 13.5px; color: var(--text-primary); line-height: 1.55; font-family: inherit; margin-bottom: 12px;">{!! $note->formatted_content !!}</div>
        @endif

    </div>

    <!-- Card Footer -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px; padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.06); font-size: 11.5px;">
        <div style="display: flex; align-items: center; gap: 6px; color: var(--text-muted); flex-wrap: wrap;">
            @if($note->user)
                <span style="display: inline-flex; align-items: center; gap: 4px; font-weight: 600; color: var(--text-secondary);" title="Ditulis oleh: {{ $note->user->name }}">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 50%; background: rgba(255,255,255,0.08); border: 1px solid var(--border); font-size: 9.5px; font-weight: 700; color: var(--text-primary);">
                        {{ strtoupper(substr($note->user->name, 0, 1)) }}
                    </span>
                    <span>{{ $authorName }}</span>
                </span>
                <span style="opacity: 0.4;">•</span>
            @endif
            <span>{{ $note->created_at ? $note->created_at->diffForHumans() : '' }}</span>
        </div>

        <!-- Hover Action Toolbar -->
        <div class="note-actions" style="display: flex; gap: 6px; align-items: center;" onclick="event.stopPropagation();">
            @if($note->user_id === auth()->id() || auth()->user()->hasAnyRole(['Super Admin', 'Admin']))
            <!-- Delete Button -->
            <form action="{{ route('admin.notes.destroy', $note->id) }}" method="POST" class="delete-form" style="margin: 0;" data-confirm-title="Hapus Catatan?" data-confirm-text="Catatan ini akan dihapus secara permanen.">
                @csrf
                @method('DELETE')
                <button type="submit" style="background: none; border: none; cursor: pointer; font-size: 13px; opacity: 0.7; padding: 2px;" title="Hapus catatan">
                    🗑️
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
