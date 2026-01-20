@extends('layouts.master')

@section('title', 'Manajemen Master Seragam | IGI')

@section('title-sub', ' Dashboard Manajemen Master Seragam ')
@section('pagetitle', 'Manajemen Master Seragam')
@section('css')
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!--datatable css-->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <!--datatable responsive css-->
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
  <style>
    .igi-actions {
      display: flex;
      gap: .5rem;
      flex-wrap: wrap;
    }

    .igi-actions .btn {
      white-space: nowrap;
    }

    @media (max-width: 575.98px) {
      .igi-actions {
        flex-direction: column;
        width: 100%;
      }

      .igi-actions .btn {
        width: 100%;
      }
    }
  </style>
@endsection
@section('content')

  <div class="row">
    {{-- SweetAlert2 notification --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
          Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: @json(session('success')),
            timer: 2000,
            showConfirmButton: false
          });
        @endif
        @if(session('error'))
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: @json(session('error')),
            timer: 2500,
            showConfirmButton: false
          });
        @endif
                                          });
    </script>
    <div class="col-12">
      <div class="card">
        <!--start::card-->
        <div
          class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
          <h5 class="card-title mb-0"> Master Seragam </h5>
          <div class="igi-actions">
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
              <i class="fas fa-plus"></i> Tambah Item
            </button>
            <a href="{{ route('admin.uniforms.stock') }}" class="btn btn-secondary btn-sm">
              <i class="fas fa-warehouse"></i> Stok Masuk
            </a>
            <a href="{{ route('admin.uniforms.distribution') }}" class="btn btn-primary btn-sm">
              <i class="fas fa-people-carry-box"></i> Distribusi
            </a>
          </div>
        </div>
        <div class="card-body">
          <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
            <thead>
              <tr>
                <th>No</th>
                <th>Kode Item</th>
                <th>Nama Item</th>
                <th>Kategori</th>
                <th>Ukuran</th>
                <th>Warna</th>
                <th>Lokasi</th>
                <th>Stok</th>
                <th>Stok Minimum</th>
                <th>Status</th>
                <th>Terakhir Diperbarui</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($items as $item)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $item->item_code }}</td>
                  <td>{{ $item->item_name }}</td>
                  <td>{{ $item->category }}</td>
                  <td>{{ $item->sizeMaster?->code ?? $item->size ?? '-' }}</td>
                  <td>{{ $item->color ?? '-' }}</td>
                  <td>{{ $item->location }}</td>
                  <td>
                    @php
                      $isLow = $item->min_stock !== null && $item->current_stock <= $item->min_stock;
                    @endphp
                    <span class="badge {{ $isLow ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                      {{ $item->current_stock }} {{ $item->uom }}
                    </span>
                  </td>
                  <td>{{ $item->min_stock ?? '-' }}</td>
                  <td>
                    <span
                      class="badge {{ $item->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                      {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                  </td>
                  <td>{{ $item->last_updated ? \Carbon\Carbon::parse($item->last_updated)->format('d-m-Y H:i') : '-' }}</td>
                  <td>
                    <form action="{{ route('admin.uniforms.items.toggle', $item->id) }}" method="POST"
                      style="display:inline-block">
                      @csrf
                      <button type="submit"
                        class="btn btn-sm {{ $item->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                        title="Ubah Status Aktif">
                        <i class="fas {{ $item->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                      </button>
                    </form>

                    <button type="button" class="btn btn-sm btn-outline-primary" title="Edit"
                      data-bs-toggle="modal" data-bs-target="#editItemModal"
                      data-id="{{ $item->id }}"
                      data-update-url="{{ route('admin.uniforms.items.update', $item->id) }}"
                      data-item-name="{{ $item->item_name }}"
                      data-category="{{ $item->category }}"
                      data-color="{{ $item->color ?? '' }}"
                      data-uom="{{ $item->uom }}"
                      data-min-stock="{{ $item->min_stock ?? '' }}"
                      data-notes="{{ $item->notes ?? '' }}">
                      <i class="fas fa-pen"></i>
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <!--end::card-->
    </div>
  </div>

  <!-- Add Item Modal -->
  <div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form method="POST" action="{{ route('admin.uniforms.items.store') }}">
          @csrf
          <input type="hidden" name="modal_context" value="create_item">
          <div class="modal-header">
            <h5 class="modal-title">Tambah Master Seragam</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            @if($errors->any() && old('modal_context') === 'create_item')
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Nama Item</label>
                <select name="item_name" class="form-select" required>
                  <option value="">-- pilih nama item --</option>
                  @foreach($itemNames as $n)
                    <option value="{{ $n->name }}" {{ old('item_name') === $n->name ? 'selected' : '' }}>
                      {{ $n->name }}
                    </option>
                  @endforeach
                </select>
                {{-- <small class="text-muted">Kelola di <a href="{{ route('admin.uniform_item_names.index') }}">Master Data → Nama Item Seragam</a>.</small> --}}
              </div>
              <div class="col-md-6">
                <label class="form-label">Kategori</label>
                <select name="category" class="form-select" required>
                  <option value="">-- pilih kategori --</option>
                  @foreach($categories as $c)
                    <option value="{{ $c->name }}" {{ old('category', 'Uniform') === $c->name ? 'selected' : '' }}>
                      {{ $c->name }}
                    </option>
                  @endforeach
                </select>
                {{-- <small class="text-muted">Kelola di <a href="{{ route('admin.uniform_categories.index') }}">Master Data → Kategori Seragam</a>.</small> --}}
              </div>
              <div class="col-md-4">
                <label class="form-label">Ukuran</label>
                <select name="uniform_size_id" class="form-select" required>
                  <option value="">-- pilih ukuran --</option>
                  @foreach($sizes as $s)
                    @php
                      $sizeLabel = $s->code;
                      if (!empty($s->name) && $s->name !== $s->code) {
                        $sizeLabel .= ' - ' . $s->name;
                      }
                    @endphp
                    <option value="{{ $s->id }}" {{ (string) old('uniform_size_id') === (string) $s->id ? 'selected' : '' }}>
                      {{ $sizeLabel }}
                    </option>
                  @endforeach
                </select>
                {{-- <small class="text-muted">Kelola ukuran di <a href="{{ route('admin.uniform_sizes.index') }}">Master Data → Ukuran Seragam</a>.</small> --}}
              </div>
              <div class="col-md-4">
                <label class="form-label">Warna</label>
                <select name="color" class="form-select">
                  <option value="">-- (opsional) --</option>
                  @foreach($colors as $c)
                    <option value="{{ $c->name }}" {{ old('color') === $c->name ? 'selected' : '' }}>
                      {{ $c->name }}
                    </option>
                  @endforeach
                </select>
                {{-- <small class="text-muted">Kelola di <a href="{{ route('admin.uniform_colors.index') }}">Master Data → Warna Seragam</a>.</small> --}}
              </div>
              <div class="col-md-4">
                <label class="form-label">UOM</label>
                <select name="uom" class="form-select" required>
                  <option value="">-- pilih uom --</option>
                  @foreach($uoms as $u)
                    @php
                      $uomLabel = $u->code;
                      if (!empty($u->name) && $u->name !== $u->code) {
                        $uomLabel .= ' - ' . $u->name;
                      }
                    @endphp
                    <option value="{{ $u->code }}" {{ (old('uom', 'pcs') === $u->code) ? 'selected' : '' }}>
                      {{ $uomLabel }}
                    </option>
                  @endforeach
                </select>
                {{-- <small class="text-muted">Kelola di <a href="{{ route('admin.uniform_uoms.index') }}">Master Data → UOM Seragam</a>.</small> --}}
              </div>
              <div class="col-md-6">
                <label class="form-label">Lokasi</label>
                <select name="location" class="form-select" required>
                  <option value="Jababeka" {{ old('location', 'Jababeka') === 'Jababeka' ? 'selected' : '' }}>Jababeka</option>
                  <option value="Karawang" {{ old('location') === 'Karawang' ? 'selected' : '' }}>Karawang</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Stok Minimum (Reorder Point)</label>
                <input type="number" name="min_stock" class="form-control" min="0" placeholder="ex: 5" value="{{ old('min_stock') }}">
              </div>
              <div class="col-12">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-success">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Item Modal -->
  <div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        @php
          $editId = old('modal_context') === 'edit_item' ? (int) old('id') : null;
        @endphp
        <form method="POST" id="editItemForm" action="{{ $editId ? route('admin.uniforms.items.update', $editId) : '#' }}">
          @csrf
          @method('PUT')
          <input type="hidden" name="modal_context" value="edit_item">
          <input type="hidden" name="id" id="edit_id" value="{{ old('id') }}">
          <div class="modal-header">
            <h5 class="modal-title">Edit Master Seragam</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            @if($errors->any() && old('modal_context') === 'edit_item')
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Nama Item</label>
                <select name="item_name" id="edit_item_name" class="form-select" required>
                  <option value="">-- pilih nama item --</option>
                  @foreach($itemNames as $n)
                    <option value="{{ $n->name }}" {{ old('item_name') === $n->name ? 'selected' : '' }}>
                      {{ $n->name }}
                    </option>
                  @endforeach
                </select>
                {{-- <small class="text-muted">Kelola di <a href="{{ route('admin.uniform_item_names.index') }}">Master Data → Nama Item Seragam</a>.</small> --}}
              </div>
              <div class="col-md-6">
                <label class="form-label">Kategori</label>
                <select name="category" id="edit_category" class="form-select" required>
                  <option value="">-- pilih kategori --</option>
                  @foreach($categories as $c)
                    <option value="{{ $c->name }}" {{ old('category') === $c->name ? 'selected' : '' }}>
                      {{ $c->name }}
                    </option>
                  @endforeach
                </select>
                {{-- <small class="text-muted">Kelola di <a href="{{ route('admin.uniform_categories.index') }}">Master Data → Kategori Seragam</a>.</small> --}}
              </div>
              <div class="col-md-6">
                <label class="form-label">Warna</label>
                <select name="color" id="edit_color" class="form-select">
                  <option value="">-- (opsional) --</option>
                  @foreach($colors as $c)
                    <option value="{{ $c->name }}" {{ old('color') === $c->name ? 'selected' : '' }}>
                      {{ $c->name }}
                    </option>
                  @endforeach
                </select>
                {{-- <small class="text-muted">Kelola di <a href="{{ route('admin.uniform_colors.index') }}">Master Data → Warna Seragam</a>.</small> --}}
              </div>
              <div class="col-md-6">
                <label class="form-label">UOM</label>
                <select name="uom" id="edit_uom" class="form-select" required>
                  <option value="">-- pilih uom --</option>
                  @foreach($uoms as $u)
                    @php
                      $uomLabel = $u->code;
                      if (!empty($u->name) && $u->name !== $u->code) {
                        $uomLabel .= ' - ' . $u->name;
                      }
                    @endphp
                    <option value="{{ $u->code }}" {{ old('uom') === $u->code ? 'selected' : '' }}>
                      {{ $uomLabel }}
                    </option>
                  @endforeach
                </select>
                {{-- <small class="text-muted">Kelola di <a href="{{ route('admin.uniform_uoms.index') }}">Master Data → UOM Seragam</a>.</small> --}}
              </div>
              <div class="col-md-6">
                <label class="form-label">Stok Minimum (Reorder Point)</label>
                <input type="number" name="min_stock" id="edit_min_stock" class="form-control" min="0" placeholder="ex: 5" value="{{ old('min_stock') }}">
              </div>
              <div class="col-12">
                <label class="form-label">Catatan</label>
                <textarea name="notes" id="edit_notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
              </div>
              <div class="col-12">
                <div class="alert alert-info mb-0">
                  Field yang diubah: <strong>Nama Item</strong>, <strong>Kategori</strong>, <strong>Warna</strong>, <strong>UOM</strong>, <strong>Stok Minimum</strong>, <strong>Catatan</strong>.
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

@endsection

@section('js')

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

  <!--datatable js-->
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

  <script src="{{ asset('assets/js/table/datatable.init.js') }}"></script>
  <script type="module" src="{{ asset('assets/js/app.js') }}"></script>

  <script>
    // Keep SweetAlert2 available on this page for future confirmations
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const editModal = document.getElementById('editItemModal');
      if (!editModal) return;

      const ensureOption = (selectEl, value, label) => {
        if (!selectEl) return;
        const v = (value ?? '').toString();
        if (!v) return;
        const found = Array.from(selectEl.options).some((opt) => opt.value === v);
        if (found) return;

        const opt = document.createElement('option');
        opt.value = v;
        opt.textContent = label || (v + ' (belum terdaftar)');
        selectEl.insertBefore(opt, selectEl.options[0] || null);
      };

      editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        if (!button) return;

        const form = document.getElementById('editItemForm');
        const updateUrl = button.getAttribute('data-update-url');
        if (form && updateUrl) form.setAttribute('action', updateUrl);

        const id = button.getAttribute('data-id') || '';
        const idEl = document.getElementById('edit_id');
        if (idEl) idEl.value = id;

        const itemName = button.getAttribute('data-item-name') || '';
        const category = button.getAttribute('data-category') || '';
        const color = button.getAttribute('data-color') || '';
        const uom = button.getAttribute('data-uom') || '';
        const minStock = button.getAttribute('data-min-stock') || '';
        const notes = button.getAttribute('data-notes') || '';

        const itemNameEl = document.getElementById('edit_item_name');
        const categoryEl = document.getElementById('edit_category');
        const colorEl = document.getElementById('edit_color');
        const uomEl = document.getElementById('edit_uom');
        const minStockEl = document.getElementById('edit_min_stock');
        const notesEl = document.getElementById('edit_notes');

        ensureOption(itemNameEl, itemName, itemName ? (itemName + ' (belum terdaftar)') : '');
        ensureOption(categoryEl, category, category ? (category + ' (belum terdaftar)') : '');
        ensureOption(colorEl, color, color ? (color + ' (belum terdaftar)') : '');
        ensureOption(uomEl, uom, uom ? (uom + ' (belum terdaftar)') : '');

        if (itemNameEl) itemNameEl.value = itemName;
        if (categoryEl) categoryEl.value = category;
        if (colorEl) colorEl.value = color;
        if (uomEl) uomEl.value = uom;
        if (minStockEl) minStockEl.value = minStock;
        if (notesEl) notesEl.value = notes;
      });

      @if($errors->any() && old('modal_context') === 'create_item')
        const addModal = document.getElementById('addItemModal');
        if (addModal) new bootstrap.Modal(addModal).show();
      @endif

      @if($errors->any() && old('modal_context') === 'edit_item')
        if (editModal) new bootstrap.Modal(editModal).show();
      @endif
    });
  </script>

@endsection