<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    /**
     * Display a listing of notes (Google Keep-style).
     */
    public function index(Request $request)
    {
        $query = Note::where('user_id', Auth::id())
            ->where('is_archived', false);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($color = $request->input('color')) {
            if ($color !== 'all') {
                $query->where('color', $color);
            }
        }

        $allNotes = $query->orderBy('sort_order', 'asc')->latest()->get();

        $pinnedNotes = $allNotes->where('is_pinned', true)->values();
        $otherNotes = $allNotes->where('is_pinned', false)->values();

        return view('admin.notes.index', compact('pinnedNotes', 'otherNotes'));
    }

    /**
     * Reorder notes & update pinned status via drag and drop.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'pinned' => 'nullable|array',
            'pinned.*' => 'integer|exists:notes,id',
            'others' => 'nullable|array',
            'others.*' => 'integer|exists:notes,id',
        ]);

        if ($request->has('pinned')) {
            foreach ((array)$request->input('pinned') as $index => $id) {
                $note = Note::where('id', $id)->where('user_id', Auth::id())->first();
                if ($note) {
                    $note->update([
                        'is_pinned' => true,
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        if ($request->has('others')) {
            foreach ((array)$request->input('others') as $index => $id) {
                $note = Note::where('id', $id)->where('user_id', Auth::id())->first();
                if ($note) {
                    $note->update([
                        'is_pinned' => false,
                        'sort_order' => $index,
                    ]);
                }
            }
        }


        return response()->json(['status' => 'success', 'message' => 'Urutan catatan berhasil diperbarui']);
    }


    /**
     * Store a newly created note in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'nullable|string|max:255',
            'content' => 'required_without:title|nullable|string',
            'color'   => 'nullable|string|in:default,purple,blue,green,amber,red',
            'is_pinned' => 'nullable|boolean',
        ]);

        if (empty(trim($validated['title'] ?? '')) && empty(trim($validated['content'] ?? ''))) {
            return back()->withErrors(['content' => 'Catatan tidak boleh kosong.']);
        }

        Note::create([
            'user_id'   => Auth::id(),
            'title'     => $validated['title'] ?? null,
            'content'   => $validated['content'] ?? null,
            'color'     => $validated['color'] ?? 'default',
            'is_pinned' => $request->boolean('is_pinned'),
        ]);

        return redirect()->route('admin.notes.index')->with('success', 'Catatan berhasil disimpan!');
    }

    /**
     * Update the specified note in storage.
     */
    public function update(Request $request, Note $note)
    {
        $this->authorizeOwner($note);

        $validated = $request->validate([
            'title'   => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'color'   => 'nullable|string|in:default,purple,blue,green,amber,red',
            'is_pinned' => 'nullable|boolean',
        ]);

        $validated['is_pinned'] = $request->has('is_pinned') ? $request->boolean('is_pinned') : $note->is_pinned;


        $note->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'note' => $note]);
        }

        return redirect()->route('admin.notes.index')->with('success', 'Catatan berhasil diperbarui!');
    }

    /**
     * Toggle the pinned status of a note.
     */
    public function togglePin(Note $note)
    {
        $this->authorizeOwner($note);

        $note->update(['is_pinned' => !$note->is_pinned]);

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'is_pinned' => $note->is_pinned]);
        }

        return back()->with('success', $note->is_pinned ? 'Catatan disematkan!' : 'Sematkan catatan dilepas.');
    }

    /**
     * Update note color.
     */
    public function updateColor(Request $request, Note $note)
    {
        $this->authorizeOwner($note);

        $validated = $request->validate([
            'color' => 'required|string|in:default,purple,blue,green,amber,red',
        ]);

        $note->update(['color' => $validated['color']]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'color' => $note->color]);
        }

        return back();
    }

    /**
     * Remove the specified note from storage.
     */
    public function destroy(Note $note)
    {
        $this->authorizeOwner($note);

        $note->delete();

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success']);
        }

        return redirect()->route('admin.notes.index')->with('success', 'Catatan berhasil dihapus!');
    }

    /**
     * Ensure current user owns the note.
     */
    protected function authorizeOwner(Note $note)
    {
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this note.');
        }
    }
}
