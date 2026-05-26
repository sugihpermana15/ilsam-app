@extends('layouts.master')

@section('title', 'Notes | ILSAM')
@section('title-sub', 'Notes')
@section('pagetitle', 'Notes')

@section('content')
  @php
    $q = $q ?? '';
    $status = $status ?? '';
    $category = $category ?? '';
    $categories = $categories ?? collect();
  @endphp

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="row g-3">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
          <div>
            <h5 class="card-title mb-0">Notes</h5>
            <div class="text-muted small">Catatan pribadi (hanya dapat diakses oleh pembuatnya).</div>
          </div>
          <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.notes.create') }}" class="btn btn-success btn-sm">
              <i class="fas fa-plus"></i> Add Note
            </a>
          </div>
        </div>

        <div class="card-body">
          <form method="GET" class="row g-2 align-items-end mb-3">
            <div class="col-12 col-md-5">
              <label class="form-label">Search Title</label>
              <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Search by title...">
            </div>
            <div class="col-12 col-md-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="">- All -</option>
                <option value="draft" @selected($status === 'draft')>Draft</option>
                <option value="published" @selected($status === 'published')>Published</option>
              </select>
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">Category</label>
              <select name="category" class="form-select">
                <option value="">- All -</option>
                @foreach($categories as $c)
                  <option value="{{ $c }}" @selected($category === $c)>{{ $c }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12 col-md-auto">
              <button class="btn btn-primary" type="submit">Filter</button>
            </div>
            <div class="col-12 col-md-auto">
              <a class="btn btn-outline-secondary" href="{{ route('admin.notes.index') }}">Reset</a>
            </div>
          </form>

          <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
              <thead>
                <tr>
                  <th style="width: 160px;">Created</th>
                  <th>Title</th>
                  <th style="width: 180px;">Category</th>
                  <th style="width: 160px;">Status</th>
                  <th style="width: 220px;">Tags</th>
                  <th style="width: 150px;">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($notes as $note)
                  <tr>
                    <td>
                      <div class="fw-semibold">{{ optional($note->created_at)->format('d M Y') }}</div>
                      <div class="text-muted small">{{ optional($note->created_at)->format('H:i') }}</div>
                    </td>
                    <td>
                      <div class="fw-semibold">{{ $note->title }}</div>
                      <div class="text-muted small" style="max-width: 640px;">{{ \Illuminate\Support\Str::limit(strip_tags($note->content), 120) }}</div>
                    </td>
                    <td>{{ $note->category ?? '-' }}</td>
                    <td>
                      @if($note->status === 'published')
                        <span class="badge bg-success-subtle text-success">Published</span>
                      @else
                        <span class="badge bg-secondary-subtle text-secondary">Draft</span>
                      @endif
                    </td>
                    <td>{{ $note->tags ?? '-' }}</td>
                    <td>
                      <div class="d-flex gap-2 flex-wrap">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.notes.edit', $note) }}">
                          <i class="fas fa-pen"></i>
                        </a>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.notes.show', $note) }}">
                          <i class="fas fa-eye"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.notes.destroy', $note) }}" class="js-delete-note">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6">
                      <div class="alert alert-info mb-0">No notes found.</div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          @if(method_exists($notes, 'links'))
            <div class="d-flex justify-content-end">
              {{ $notes->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection

@section('js')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.js-delete-note').forEach((form) => {
        form.addEventListener('submit', function (e) {
          e.preventDefault();
          Swal.fire({
            title: 'Delete note?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
          }).then((result) => {
            if (result.isConfirmed) {
              form.submit();
            }
          });
        });
      });
    });
  </script>
@endsection
