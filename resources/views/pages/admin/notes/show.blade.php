@extends('layouts.master')

@section('title', 'Note Detail | ILSAM')
@section('title-sub', 'Notes')
@section('pagetitle', 'Note Detail')

@section('content')
  <div class="row g-3">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
          <div>
            <h5 class="card-title mb-0">{{ $note->title }}</h5>
            <div class="text-muted small">
              {{ optional($note->created_at)->format('d M Y H:i') }}
              @if($note->category)
                • {{ $note->category }}
              @endif
              @if($note->status)
                • {{ ucfirst($note->status) }}
              @endif
            </div>
          </div>
          <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.notes.edit', $note) }}" class="btn btn-outline-primary btn-sm">
              <i class="fas fa-pen"></i> Edit
            </a>
            <a href="{{ route('admin.notes.index') }}" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-arrow-left"></i> Back
            </a>
          </div>
        </div>

        <div class="card-body">
          @if($note->tags)
            <div class="mb-3">
              <span class="text-muted">Tags:</span> {{ $note->tags }}
            </div>
          @endif

          <div class="border rounded p-3" style="white-space: pre-wrap;">{{ $note->content }}</div>
        </div>
      </div>
    </div>
  </div>
@endsection
