@extends('layouts.master')

@section('title', 'Add Note | ILSAM')
@section('title-sub', 'Notes')
@section('pagetitle', 'Add Note')

@section('content')
  <div class="row g-3">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
          <div>
            <h5 class="card-title mb-0">Add Note</h5>
            <div class="text-muted small">Buat catatan baru.</div>
          </div>
          <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.notes.index') }}" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-arrow-left"></i> Back
            </a>
          </div>
        </div>

        <div class="card-body">
          @if($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('admin.notes.store') }}" class="row g-3">
            @csrf

            <div class="col-12">
              <label class="form-label">Title</label>
              <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>

            <div class="col-12">
              <label class="form-label">Content</label>
              <textarea name="content" class="form-control" rows="8" required>{{ old('content') }}</textarea>
            </div>

            <div class="col-md-4">
              <label class="form-label">Status</label>
              <select name="status" class="form-select" required>
                <option value="draft" @selected(old('status', 'draft') === 'draft')>Draft</option>
                <option value="published" @selected(old('status') === 'published')>Published</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Category</label>
              <input type="text" name="category" class="form-control" value="{{ old('category') }}" placeholder="Optional">
            </div>

            <div class="col-md-4">
              <label class="form-label">Tags</label>
              <input type="text" name="tags" class="form-control" value="{{ old('tags') }}" placeholder="Optional (comma separated)">
            </div>

            <div class="col-12 d-flex gap-2">
              <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Save
              </button>
              <a href="{{ route('admin.notes.index') }}" class="btn btn-light">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
