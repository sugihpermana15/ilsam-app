@extends('layouts.master')

@section('title', 'Rekrutmen - Detail Form')

@section('title-sub', 'Recruitment')
@section('pagetitle', 'Detail Form Rekrutmen')

@section('css')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @php
        $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'recruitment', 'update');
        $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'recruitment', 'create');
        $canDelete = \App\Support\MenuAccess::can(auth()->user(), 'recruitment', 'delete');
        $publicUrl = url(route('recruitment.form.show', ['token' => $form->public_token], false));
    @endphp

    <div class="row">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: @json(__('common.success')),
                        text: @json(session('success')),
                        timer: 2000,
                        showConfirmButton: false
                    });
                @endif
                @if (session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: @json(__('common.error')),
                        text: @json(session('error')),
                        timer: 2500,
                        showConfirmButton: false
                    });
                @endif
            });
        </script>

        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                    <div>
                        <h5 class="card-title mb-0">{{ $form->title }}</h5>
                        <div class="text-muted small">Posisi: {{ $form->position_name }} ({{ $form->position_code_initial }})</div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.recruitment.forms.index') }}">Kembali</a>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.recruitment.candidates.index') }}">List Kandidat</a>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateFormModal" {{ $canUpdate ? '' : 'disabled' }}>
                            Edit Form
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <label class="form-label">Link Publik</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ $publicUrl }}" readonly>
                                <button type="button" class="btn btn-outline-secondary" id="btnCopyPublicLink" data-url="{{ $publicUrl }}">Copy</button>
                            </div>
                            <div class="form-text">Kandidat mengisi tanpa login melalui link ini.</div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Status</label>
                            <div>
                                @if($form->is_active)
                                    <span class="badge bg-success-subtle text-success">AKTIF</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">NONAKTIF</span>
                                @endif
                                @if($form->is_security_position)
                                    <span class="badge bg-warning-subtle text-warning ms-1">SECURITY</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary ms-1">NON-SECURITY</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" />

                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-2">
                        <h6 class="mb-0">Pertanyaan Tes Pengetahuan</h6>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addQuestionModal" {{ $canCreate ? '' : 'disabled' }}>
                            + Tambah Pertanyaan
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table id="recruitment-questions-table" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Urut</th>
                                    <th>Tipe</th>
                                    <th>Pertanyaan</th>
                                    <th>Wajib</th>
                                    <th>Poin</th>
                                    <th>Opsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.recruitment.forms.update', $form->id) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="modal_context" value="update_recruitment_form">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Form</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any() && old('modal_context') === 'update_recruitment_form')
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Judul Form</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $form->title) }}" required>
                            </div>

                            <div class="col-12 col-md-8">
                                <label class="form-label">Nama Posisi</label>
                                <input type="text" name="position_name" class="form-control" value="{{ old('position_name', $form->position_name) }}" required>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label">Inisial Kode Posisi</label>
                                <input type="text" name="position_code_initial" class="form-control" value="{{ old('position_code_initial', $form->position_code_initial) }}" maxlength="20" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="edit_is_security" name="is_security_position" value="1" @checked(old('is_security_position', $form->is_security_position))>
                                    <label class="form-check-label" for="edit_is_security">Posisi Security</label>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1" @checked(old('is_active', $form->is_active))>
                                    <label class="form-check-label" for="edit_is_active">Aktif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" {{ $canUpdate ? '' : 'disabled' }}>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addQuestionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.recruitment.questions.store') }}">
                    @csrf
                    <input type="hidden" name="recruitment_form_id" value="{{ $form->id }}">
                    <input type="hidden" name="modal_context" value="add_question">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Pertanyaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any() && old('modal_context') === 'add_question')
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label">Tipe</label>
                                <select name="type" class="form-select" id="add_q_type" required>
                                    <option value="multiple_choice" @selected(old('type') === 'multiple_choice')>Pilihan Ganda</option>
                                    <option value="short_text" @selected(old('type') === 'short_text')>Isian Singkat</option>
                                    <option value="essay" @selected(old('type') === 'essay')>Essay</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label">Urutan</label>
                                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label">Poin (opsional)</label>
                                <input type="number" name="points" class="form-control" value="{{ old('points', 0) }}" min="0">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Pertanyaan</label>
                                <textarea name="question_text" class="form-control" rows="3" required>{{ old('question_text') }}</textarea>
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="add_q_required" name="is_required" value="1" @checked(old('is_required', true))>
                                    <label class="form-check-label" for="add_q_required">Wajib dijawab</label>
                                </div>
                            </div>

                            <div class="col-12" id="add_q_options_wrap">
                                <label class="form-label">Opsi (satu baris = satu opsi)</label>
                                <textarea name="options_text" class="form-control" rows="4" placeholder="Contoh:\nA\nB\nC\nD">{{ old('options_text') }}</textarea>
                            </div>

                            <div class="col-12" id="add_q_correct_wrap">
                                <label class="form-label">Jawaban Benar (nomor opsi)</label>
                                <input type="number" name="correct_option" class="form-control" value="{{ old('correct_option') }}" min="1" placeholder="Contoh: 1 (opsi baris pertama)">
                                <div class="form-text">Wajib untuk Pilihan Ganda. Nomor mengikuti urutan opsi di atas.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" {{ $canCreate ? '' : 'disabled' }}>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editQuestionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form method="POST" action="#" id="editQuestionForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="modal_context" value="edit_question">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Pertanyaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any() && old('modal_context') === 'edit_question')
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label">Tipe</label>
                                <select name="type" class="form-select" id="edit_q_type" required>
                                    <option value="multiple_choice">Pilihan Ganda</option>
                                    <option value="short_text">Isian Singkat</option>
                                    <option value="essay">Essay</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label">Urutan</label>
                                <input type="number" name="sort_order" class="form-control" id="edit_q_sort_order" min="0">
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label">Poin (opsional)</label>
                                <input type="number" name="points" class="form-control" id="edit_q_points" min="0">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Pertanyaan</label>
                                <textarea name="question_text" class="form-control" rows="3" id="edit_q_text" required></textarea>
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_q_required" name="is_required" value="1">
                                    <label class="form-check-label" for="edit_q_required">Wajib dijawab</label>
                                </div>
                            </div>

                            <div class="col-12" id="edit_q_options_wrap">
                                <label class="form-label">Opsi (satu baris = satu opsi)</label>
                                <textarea name="options_text" class="form-control" rows="4" id="edit_q_options"></textarea>
                            </div>

                            <div class="col-12" id="edit_q_correct_wrap">
                                <label class="form-label">Jawaban Benar (nomor opsi)</label>
                                <input type="number" name="correct_option" class="form-control" id="edit_q_correct" min="1" placeholder="Contoh: 1">
                                <div class="form-text">Wajib untuk Pilihan Ganda. Nomor mengikuti urutan opsi di atas.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" {{ $canUpdate ? '' : 'disabled' }}>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form method="POST" action="#" id="deleteQuestionForm" class="d-none">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            const dtUrl = @json(route('admin.recruitment.forms.questions.datatable', $form->id));

            async function copyTextToClipboard(text) {
                const value = String(text || '');
                if (!value) return false;

                try {
                    if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function' && window.isSecureContext) {
                        await navigator.clipboard.writeText(value);
                        return true;
                    }
                } catch (e) {
                    // fall through
                }

                try {
                    const ta = document.createElement('textarea');
                    ta.value = value;
                    ta.setAttribute('readonly', '');
                    ta.style.position = 'fixed';
                    ta.style.top = '-9999px';
                    ta.style.left = '-9999px';
                    document.body.appendChild(ta);
                    ta.select();
                    ta.setSelectionRange(0, ta.value.length);
                    const ok = document.execCommand('copy');
                    document.body.removeChild(ta);
                    return !!ok;
                } catch (e) {
                    return false;
                }
            }

            function toggleOptionsWrap(type, wrap) {
                const isMcq = (type || '') === 'multiple_choice';
                if (wrap) wrap.classList.toggle('d-none', !isMcq);
            }

            function toggleCorrectWrap(type, wrap) {
                const isMcq = (type || '') === 'multiple_choice';
                if (wrap) wrap.classList.toggle('d-none', !isMcq);
            }

            document.getElementById('btnCopyPublicLink')?.addEventListener('click', async function() {
                const url = this.getAttribute('data-url') || '';

                const ok = await copyTextToClipboard(url);
                if (ok) {
                    Swal.fire({ icon: 'success', title: @json(__('common.success')), text: 'Link berhasil disalin.', timer: 1200, showConfirmButton: false });
                    return;
                }

                Swal.fire({
                    icon: 'error',
                    title: @json(__('common.error')),
                    text: 'Browser tidak mengizinkan menyalin otomatis. Silakan blok/copy manual dari kolom Link Publik.',
                    timer: 2800,
                    showConfirmButton: false
                });
            });

            const addType = document.getElementById('add_q_type');
            const addWrap = document.getElementById('add_q_options_wrap');
            const addCorrectWrap = document.getElementById('add_q_correct_wrap');
            if (addType) {
                addType.addEventListener('change', () => {
                    toggleOptionsWrap(addType.value, addWrap);
                    toggleCorrectWrap(addType.value, addCorrectWrap);
                });
                toggleOptionsWrap(addType.value, addWrap);
                toggleCorrectWrap(addType.value, addCorrectWrap);
            }

            const editType = document.getElementById('edit_q_type');
            const editWrap = document.getElementById('edit_q_options_wrap');
            const editCorrectWrap = document.getElementById('edit_q_correct_wrap');
            if (editType) {
                editType.addEventListener('change', () => {
                    toggleOptionsWrap(editType.value, editWrap);
                    toggleCorrectWrap(editType.value, editCorrectWrap);
                });
            }

            const table = $('#recruitment-questions-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pagingType: 'full_numbers',
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                language: {
                    processing: 'Memproses...',
                    search: 'Cari : ',
                    searchPlaceholder: 'Ketik untuk memfilter...',
                    lengthMenu: 'Tampilkan _MENU_',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    infoFiltered: '(difilter dari _MAX_ total data)',
                    zeroRecords: 'Data tidak ditemukan',
                    emptyTable: 'Tidak ada data',
                    paginate: {
                        first: '<i class="ri-arrow-left-double-line"></i>',
                        previous: '<i class="ri-arrow-left-s-line"></i>',
                        next: '<i class="ri-arrow-right-s-line"></i>',
                        last: '<i class="ri-arrow-right-double-line"></i>',
                    },
                },
                dom: "<'row'<'col-sm-12 col-md-6 mb-3'l><'col-sm-12 col-md-6 mt-5'f>>" +
                    "<'table-responsive'tr>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                ajax: {
                    url: dtUrl
                },
                order: [[0, 'asc']],
                columns: [
                    { data: 'sort_order' },
                    { data: 'type_label' },
                    { data: 'question_text' },
                    {
                        data: 'is_required',
                        render: function(data) {
                            const v = !!data;
                            const cls = v ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary';
                            return `<span class="badge ${cls}">${v ? 'WAJIB' : 'OPSIONAL'}</span>`;
                        }
                    },
                    { data: 'points' },
                    {
                        data: 'options',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const arr = Array.isArray(data) ? data : [];
                            if (!arr.length) return '<span class="text-muted">-</span>';
                            const safe = arr.map(v => $('<div/>').text(String(v)).html()).join('<br>');
                            return safe;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const updateUrl = row.actions && row.actions.update_url ? String(row.actions.update_url) : '#';
                            const deleteUrl = row.actions && row.actions.delete_url ? String(row.actions.delete_url) : '#';
                            const canUpdate = @json($canUpdate);
                            const canDelete = @json($canDelete);

                            return `
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary js-edit-q" ${canUpdate ? '' : 'disabled'} data-update-url="${$('<div/>').text(updateUrl).html()}">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger js-del-q" ${canDelete ? '' : 'disabled'} data-delete-url="${$('<div/>').text(deleteUrl).html()}">
                                        Hapus
                                    </button>
                                </div>
                            `;
                        }
                    }
                ]
            });

            document.addEventListener('click', function(e) {
                const editBtn = e.target.closest('.js-edit-q');
                if (editBtn) {
                    const tr = editBtn.closest('tr');
                    const row = table.row(tr).data();
                    if (!row) return;

                    const form = document.getElementById('editQuestionForm');
                    form.setAttribute('action', editBtn.getAttribute('data-update-url'));

                    document.getElementById('edit_q_type').value = row.type || 'multiple_choice';
                    document.getElementById('edit_q_sort_order').value = row.sort_order ?? 0;
                    document.getElementById('edit_q_points').value = row.points ?? 0;
                    document.getElementById('edit_q_text').value = row.question_text || '';
                    document.getElementById('edit_q_required').checked = !!row.is_required;
                    document.getElementById('edit_q_options').value = Array.isArray(row.options) ? row.options.join('\n') : '';
                    document.getElementById('edit_q_correct').value = row.correct_option_index ?? '';

                    toggleOptionsWrap(document.getElementById('edit_q_type').value, editWrap);
                    toggleCorrectWrap(document.getElementById('edit_q_type').value, editCorrectWrap);

                    new bootstrap.Modal(document.getElementById('editQuestionModal')).show();
                    return;
                }

                const delBtn = e.target.closest('.js-del-q');
                if (delBtn) {
                    const url = delBtn.getAttribute('data-delete-url') || '#';

                    Swal.fire({
                        title: 'Hapus pertanyaan?',
                        text: 'Aksi ini tidak dapat dibatalkan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (!result.isConfirmed) return;
                        const f = document.getElementById('deleteQuestionForm');
                        f.setAttribute('action', url);
                        f.submit();
                    });
                }
            });

            @if ($errors->any() && old('modal_context') === 'update_recruitment_form')
                const editModal = document.getElementById('updateFormModal');
                if (editModal) new bootstrap.Modal(editModal).show();
            @endif

            @if ($errors->any() && old('modal_context') === 'add_question')
                const addModal = document.getElementById('addQuestionModal');
                if (addModal) new bootstrap.Modal(addModal).show();
            @endif

            @if ($errors->any() && old('modal_context') === 'edit_question')
                const editQModal = document.getElementById('editQuestionModal');
                if (editQModal) new bootstrap.Modal(editQModal).show();
            @endif
        });
    </script>
@endsection
