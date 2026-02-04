@extends('layouts.master')

@section('title', 'Website Products | ILSAM')
@section('title-sub', 'Website')
@section('pagetitle', 'Website Products')

@section('content')
  @php
    $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'website_products', 'create');
    $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'website_products', 'update');
    $canDelete = \App\Support\MenuAccess::can(auth()->user(), 'website_products', 'delete');

    $products = collect($products ?? []);
  @endphp

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
          <div>
            <h5 class="card-title mb-0">Website Products</h5>
            <div class="text-muted small">Manage product pages content (PU Resin, Additive Coating, etc).</div>
          </div>
          <div class="d-flex gap-2 flex-wrap">
            <a href="{{ url('/products') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-external-link-alt"></i> Preview Products
            </a>
            <a href="{{ route('admin.website_products.create') }}" class="btn btn-success btn-sm" {{ $canCreate ? '' : 'disabled' }}>
              <i class="fas fa-plus"></i> Add Product
            </a>
          </div>
        </div>

        <div class="card-body table-responsive">
          @if($products->isEmpty())
            <div class="alert alert-info mb-0">No products yet.</div>
          @else
            <table class="table table-striped table-bordered align-middle">
              <thead>
                <tr>
                  <th>Slug</th>
                  <th>Title</th>
                  <th>Status</th>
                  <th>Sort</th>
                  <th>Preview</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($products as $p)
                  @php
                    $slug = (string) ($p['slug'] ?? '');
                    $previewUrl = match ($slug) {
                      'colorants' => route('products.colorants'),
                      'surface-coating-agents' => route('products.surface-coating-agents'),
                      'additive-coating' => route('products.additive-coating'),
                      'pu-resin' => route('products.pu-resin'),
                      default => route('products'),
                    };
                  @endphp
                  <tr>
                    <td><span class="badge bg-light text-dark">{{ $slug }}</span></td>
                    <td>
                      <div class="fw-semibold">{{ $p['title'] ?? '-' }}</div>
                      <div class="text-muted small">{{ $p['tagline'] ?? '' }}</div>
                    </td>
                    <td>
                      @php $isActive = (bool) ($p['is_active'] ?? true); @endphp
                      <span class="badge bg-{{ $isActive ? 'success' : 'secondary' }}">{{ $isActive ? 'Active' : 'Inactive' }}</span>
                    </td>
                    <td>{{ (int) ($p['sort_order'] ?? 0) }}</td>
                    <td>
                      <a class="btn btn-sm btn-outline-secondary" href="{{ $previewUrl }}" target="_blank">
                        <i class="fas fa-eye"></i>
                      </a>
                    </td>
                    <td>
                      <div class="d-flex gap-2">
                        <a href="{{ route('admin.website_products.edit', $p['id']) }}" class="btn btn-sm btn-outline-primary" {{ $canUpdate ? '' : 'disabled' }}>
                          <i class="fas fa-pencil-alt"></i>
                        </a>
                        <form action="{{ route('admin.website_products.destroy', $p['id']) }}" method="POST" onsubmit="return confirm('Delete this product?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline-danger" {{ $canDelete ? '' : 'disabled' }}>
                            <i class="fas fa-trash-alt"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
      </div>

      <div class="alert alert-warning mt-3">
        <div class="fw-semibold">Note</div>
        <div class="small">Slug dibatasi ke 4 halaman produk yang sudah ada rutenya, supaya link publik tidak rusak.</div>
      </div>
    </div>
  </div>
@endsection
