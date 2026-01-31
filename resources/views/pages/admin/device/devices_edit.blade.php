@extends('layouts.master')

@section('title', 'Edit Device | IGI')
@section('title-sub', 'Master Device')
@section('pagetitle', 'Edit Device')

@section('css')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
  <style>
    .select2-container--bootstrap-5 .select2-selection {
      border-color: var(--bs-border-color);
    }
    .select2-container--bootstrap-5.select2-container--open .select2-selection,
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5 .select2-selection:focus {
      border-color: var(--bs-primary);
      box-shadow: 0 0 0 .25rem rgba(var(--bs-primary-rgb), .25);
    }
    .select2-container--bootstrap-5 .select2-dropdown .select2-results__option--highlighted {
      background-color: var(--bs-primary);
      color: #fff;
    }
    .select2-container--bootstrap-5 .select2-dropdown .select2-results__option[aria-selected="true"] {
      background-color: rgba(var(--bs-primary-rgb), .12);
      color: inherit;
    }
  </style>
@endsection

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">Edit Device</h5>
      <a href="{{ route('admin.devices.show', $device) }}" class="btn btn-light">
        <i class="fas fa-arrow-left"></i> {{ __('common.back') }}
      </a>
    </div>
    <div class="card-body">
      @if ($errors->any())
        <div class="alert alert-danger" role="alert">
          <div class="fw-semibold mb-1">Periksa input berikut:</div>
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('admin.devices.update', $device) }}" method="POST">
        @csrf
        @method('PUT')

        @include('pages.admin.device._form', ['device' => $device])

        <div class="mt-3 d-flex gap-2">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-check-circle"></i> {{ __('common.update') }}
          </button>
          <a href="{{ route('admin.devices.show', $device) }}" class="btn btn-secondary">{{ __('common.cancel') }}</a>
        </div>
      </form>
    </div>
  </div>
@endsection
