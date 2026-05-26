<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));
        $category = trim((string) $request->query('category', ''));

        $base = Note::query()->where('created_by', auth()->id());

        $categories = (clone $base)
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->values();

        $notes = $base
            ->when($q !== '', fn ($query) => $query->where('title', 'like', '%' . $q . '%'))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.notes.index', [
            'notes' => $notes,
            'q' => $q,
            'status' => $status,
            'category' => $category,
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return view('pages.admin.notes.create');
    }

    public function store(StoreNoteRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Note::query()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category' => $validated['category'] ?? null,
            'tags' => $validated['tags'] ?? null,
            'status' => $validated['status'],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.notes.index')->with('success', 'Note created successfully.');
    }

    public function show(Note $note)
    {
        $this->authorize('view', $note);

        return view('pages.admin.notes.show', [
            'note' => $note,
        ]);
    }

    public function edit(Note $note)
    {
        $this->authorize('update', $note);

        return view('pages.admin.notes.edit', [
            'note' => $note,
        ]);
    }

    public function update(UpdateNoteRequest $request, Note $note): RedirectResponse
    {
        $this->authorize('update', $note);

        $validated = $request->validated();

        $note->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category' => $validated['category'] ?? null,
            'tags' => $validated['tags'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.notes.index')->with('success', 'Note updated successfully.');
    }

    public function destroy(Note $note): RedirectResponse
    {
        $this->authorize('delete', $note);

        $note->delete();

        return back()->with('success', 'Note deleted successfully.');
    }
}
