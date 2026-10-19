@extends('layouts.master')

@section('title', ($stageLabel ?? 'Career Candidates') . ' | ILSAM')
@section('title-sub', 'Career')
@section('pagetitle', $stageLabel ?? 'Career Candidates')

@section('css')
  <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet">
  <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
  <style>
    .career-note-preview {
      display: -webkit-box;
      width: 280px;
      max-width: 280px;
      overflow: hidden;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 2;
      line-height: 1.45;
      white-space: normal;
    }
  </style>
@endsection

@section('content')
  @php
    $stage = $stage ?? null;
    $stageLabel = $stageLabel ?? 'Career Candidates';
    $isTalentPool = $isTalentPool ?? false;
    $isFailedCandidates = $isFailedCandidates ?? false;
    $nextStageLabel = $nextStageLabel ?? null;
  @endphp

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
      <div>
        <h5 class="card-title mb-0">{{ $stageLabel }}</h5>
        <div class="text-muted small">
          {{ $isTalentPool ? 'Kandidat kompeten yang dapat dipanggil kembali untuk kebutuhan rekrutmen berikutnya.' : ($isFailedCandidates ? 'Riwayat kandidat tidak lolos beserta tahap dan alasan kegagalan.' : 'Kelola kandidat yang sedang berada pada tahap ini.') }}
        </div>
        @if($isFailedCandidates)
          <div class="text-warning small">Kandidat yang gagal sebelum fitur restore mungkin perlu posisi dan CV PDF diunggah ulang.</div>
        @endif
      </div>
      <div class="d-flex gap-2 flex-wrap">
        @if(!$isTalentPool && !$isFailedCandidates && $nextStageLabel)
          <button type="button" id="bulk-advance-candidates" class="btn btn-success btn-sm" disabled>
            <i class="fas fa-arrow-right"></i> Ke {{ $nextStageLabel }} (<span id="selected-candidate-count">0</span>)
          </button>
        @endif
        @if(!$isTalentPool && !$isFailedCandidates)
          <button type="button" id="bulk-reject-candidates" class="btn btn-outline-danger btn-sm" disabled>
            <i class="fas fa-times-circle"></i> Gagal Terpilih
          </button>
        @endif
        @if($isFailedCandidates)
          <button type="button" id="bulk-delete-failed-candidates" class="btn btn-danger btn-sm" disabled>
            <i class="fas fa-trash"></i> Hapus Terpilih (<span id="selected-candidate-count">0</span>)
          </button>
        @endif
        <a href="{{ route('admin.careers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-briefcase"></i> Job Openings</a>
        <a href="{{ route('admin.career_candidates.email_templates') }}" class="btn btn-outline-info btn-sm"><i class="fas fa-envelope"></i> Template Email</a>
        @if($isFailedCandidates)
          <a href="{{ route('admin.career_candidates.failed.export') }}" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Export Excel</a>
        @elseif(!$isTalentPool)
          <a href="{{ route('admin.career_candidates.export', ['stage' => $stage]) }}" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Export Excel</a>
          <a href="{{ route('admin.career_candidates.talent_pool') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-user-clock"></i> Talent Pool</a>
        @else
          <a href="{{ route('admin.career_candidates.index') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-alt"></i> Screening CV</a>
        @endif
      </div>
    </div>
    <div class="card-body">
      <table id="career-candidates-table" class="table table-nowrap table-striped table-bordered w-100">
        <thead>
          <tr>
            @if(!$isTalentPool)<th><input type="checkbox" id="select-all-candidates" aria-label="Pilih semua kandidat pada halaman ini"></th>@endif
            <th>Submitted</th>
            <th>Candidate</th>
            <th>Position</th>
            <th>Domicile</th>
            <th>Experience</th>
            <th>Contact</th>
            <th>Stage</th>
            <th>Notes</th>
            <th>Action</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
@endsection

@section('js')
  <script src="{{ asset('assets/js/vendor/jquery-3.7.1.min.js') }}"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap.min.js"></script>
  <script src="{{ asset('assets/js/table/datatable.init.js') }}"></script>
  <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
  <script>
    $(function () {
      const careerAlert = window.Sweetalert2;
      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
      const stage = @json($stage);
      const isTalentPool = @json($isTalentPool);
      const isFailedCandidates = @json($isFailedCandidates);
      const nextStageLabel = @json($nextStageLabel);
      const bulkAdvanceUrl = @json(route('admin.career_candidates.bulk_advance'));
      const bulkRejectUrl = @json(route('admin.career_candidates.bulk_reject'));
      const bulkDeleteUrl = @json(route('admin.career_candidates.bulk_destroy'));
      const tableUrl = isFailedCandidates
        ? @json(route('admin.career_candidates.failed.datatable'))
        : isTalentPool
          ? @json(route('admin.career_candidates.talent_pool.datatable'))
          : @json(route('admin.career_candidates.datatable', ['stage' => '__stage__'])).replace('__stage__', stage);

      const escapeHtml = (value) => $('<div/>').text(value || '').html();
      const selectedCandidateIds = new Set();
      const makeForm = (url, fields, method = 'PUT') => {
        const form = $('<form>', { method: 'POST', action: url });
        form.append($('<input>', { type: 'hidden', name: '_token', value: csrfToken }));
        form.append($('<input>', { type: 'hidden', name: '_method', value: method }));
        Object.entries(fields).forEach(([name, value]) => {
          (Array.isArray(value) ? value : [value]).forEach(fieldValue => {
            form.append($('<input>', { type: 'hidden', name, value: fieldValue }));
          });
        });
        return form;
      };
      const syncSelectedCandidates = (table) => {
        const selectedCount = selectedCandidateIds.size;
        $('#selected-candidate-count').text(selectedCount);
        $('#bulk-advance-candidates').prop('disabled', selectedCount === 0);
        $('#bulk-reject-candidates').prop('disabled', selectedCount === 0);
        $('#bulk-delete-failed-candidates').prop('disabled', selectedCount === 0);

        const checks = $(table.rows({ page: 'current' }).nodes()).find('input.select-candidate');
        const checkedCount = checks.filter(':checked').length;
        $('#select-all-candidates')
          .prop('checked', checks.length > 0 && checkedCount === checks.length)
          .prop('indeterminate', checkedCount > 0 && checkedCount < checks.length);
      };

      if ($.fn.dataTable.isDataTable('#career-candidates-table')) {
        $('#career-candidates-table').DataTable().destroy();
        $('#career-candidates-table').find('tbody').empty();
      }

      const candidateTable = $('#career-candidates-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        scrollX: true,
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pagingType: 'full_numbers',
        order: [[isTalentPool ? 0 : 1, 'desc']],
        ajax: tableUrl,
        language: {
          processing: 'Memproses...', search: 'Cari:', searchPlaceholder: 'Nama, email, telepon, posisi...',
          lengthMenu: 'Tampilkan _MENU_', info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ kandidat',
          infoEmpty: 'Tidak ada kandidat', infoFiltered: '(difilter dari _MAX_ kandidat)',
          zeroRecords: 'Kandidat tidak ditemukan', emptyTable: 'Tidak ada kandidat pada tahap ini.'
        },
        dom: "<'row'<'col-sm-12 col-md-6 mb-3'l><'col-sm-12 col-md-6 mt-5'f>>" +
          "<'table-responsive'tr>" +
          "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        columns: [
          ...(!isTalentPool ? [{ data: 'id', orderable: false, searchable: false, render: id => {
            const candidateId = String(id);
            return `<input type="checkbox" class="select-candidate" value="${candidateId}" ${selectedCandidateIds.has(candidateId) ? 'checked' : ''}>`;
          } }] : []),
          { data: 'submitted_at', defaultContent: '-' },
          { data: null, render: (data, type, row) => `<div class="fw-semibold">${escapeHtml(row.full_name)}</div><div class="text-muted small">${escapeHtml(row.email)}</div><div class="text-muted small">${escapeHtml(row.candidate_code || '-')}</div>` },
          { data: 'job_title', defaultContent: '-' },
          { data: null, render: (data, type, row) => `<div>${escapeHtml(row.domicile)}</div><div class="text-muted small">${escapeHtml(row.current_address)}</div>` },
          { data: 'experience_range', defaultContent: '-' },
          { data: 'phone', defaultContent: '-' },
          { data: null, render: (data, type, row) => row.selection_status === 'rejected'
            ? `<span class="badge bg-danger-subtle text-danger">Tidak Lolos</span><div class="small text-muted mt-1">Gagal pada ${escapeHtml(row.failed_stage_label)}</div>`
            : `<span class="badge bg-primary-subtle text-primary">${escapeHtml(row.stage_label)}</span>` },
          { data: null, orderable: false, render: (data, type, row) => {
            const note = isTalentPool ? row.talent_pool_notes : (row.stage_notes || row.message);
            const safeNote = escapeHtml(note);
            return `<span class="small text-muted career-note-preview" title="${safeNote}">${safeNote || '-'}</span>`;
          } },
          { data: null, orderable: false, searchable: false, render: (data, type, row) => {
            let html = '';
            if (row.has_cv) {
              const viewLabel = isFailedCandidates ? 'Lihat CV' : 'View PDF';
              html = `<button class="btn btn-sm btn-outline-primary js-view-cv" data-url="${escapeHtml(row.cv_view_url)}"><i class="fas fa-file-pdf"></i> ${viewLabel}</button>`;
            } else if (isFailedCandidates) {
              html = '<span class="badge bg-secondary-subtle text-muted">CV tidak tersedia</span>';
            }
            if (!isTalentPool && nextStageLabel && row.advance_url) {
              html += ` <button class="btn btn-sm btn-success js-advance" data-url="${escapeHtml(row.advance_url)}"><i class="fas fa-arrow-right"></i> Ke ${escapeHtml(nextStageLabel)}</button>`;
            }
            if (!isTalentPool && stage === 'user_site_interview' && row.talent_pool_url && row.selection_status !== 'rejected') {
              html += ` <button class="btn btn-sm btn-outline-warning js-talent-pool" data-url="${escapeHtml(row.talent_pool_url)}"><i class="fas fa-user-clock"></i> Talent Pool</button>`;
            }
            if (row.reject_url) {
              html += ` <button class="btn btn-sm btn-outline-danger js-reject" data-url="${escapeHtml(row.reject_url)}"><i class="fas fa-times-circle"></i> Gagal</button>`;
            }
            if (row.delete_url) {
              html += ` <button class="btn btn-sm btn-danger js-delete-failed" data-url="${escapeHtml(row.delete_url)}"><i class="fas fa-trash"></i> Hapus</button>`;
            }
            if (row.restore_url) {
              html += ` <button class="btn btn-sm btn-outline-success js-restore-failed" data-url="${escapeHtml(row.restore_url)}" data-id="${escapeHtml(row.id)}" data-has-cv="${row.has_cv ? '1' : '0'}" data-needs-position="${row.needs_job_title ? '1' : '0'}"><i class="fas fa-undo"></i> Restore</button>`;
            }
            return html;
          } }
        ],
        drawCallback: function () {
          const table = this.api();
          $(table.rows({ page: 'current' }).nodes()).find('input.select-candidate').each(function () {
            this.checked = selectedCandidateIds.has(String(this.value));
          });
          syncSelectedCandidates(table);
        }
      });

      $('#select-all-candidates').on('change', function () {
        const selected = this.checked;
        $(candidateTable.rows({ page: 'current' }).nodes()).find('input.select-candidate').each(function () {
          const candidateId = String(this.value);
          this.checked = selected;
          selected ? selectedCandidateIds.add(candidateId) : selectedCandidateIds.delete(candidateId);
        });
        syncSelectedCandidates(candidateTable);
      });

      $('#career-candidates-table').on('change', '.select-candidate', function () {
        const candidateId = String(this.value);
        this.checked ? selectedCandidateIds.add(candidateId) : selectedCandidateIds.delete(candidateId);
        syncSelectedCandidates(candidateTable);
      });

      $('#bulk-advance-candidates').on('click', async function () {
        const candidateIds = Array.from(selectedCandidateIds);
        if (candidateIds.length === 0) return;

        const result = await careerAlert.fire({
          icon: 'question',
          title: 'Lanjutkan kandidat terpilih?',
          text: `${candidateIds.length} kandidat akan dipindahkan ke ${nextStageLabel}.`,
          showCancelButton: true,
          confirmButtonText: 'Ya, lanjutkan',
          cancelButtonText: 'Batal',
          confirmButtonColor: '#16a34a'
        });
        if (!result.isConfirmed) return;
        makeForm(bulkAdvanceUrl, { stage, 'candidate_ids[]': candidateIds }).appendTo('body').trigger('submit');
      });

      $('#bulk-reject-candidates').on('click', async function () {
        const candidateIds = Array.from(selectedCandidateIds);
        if (candidateIds.length === 0) return;

        const result = await careerAlert.fire({
          icon: 'warning',
          title: 'Tandai kandidat gagal?',
          text: `${candidateIds.length} kandidat disimpan sebagai riwayat gagal. CV dan data tetap disimpan agar kandidat dapat dipulihkan.`,
          input: 'textarea',
          inputPlaceholder: 'Masukkan alasan kandidat tidak lolos.',
          inputAttributes: {
            'aria-label': 'Alasan kandidat gagal',
            maxlength: 3000
          },
          showCancelButton: true,
          confirmButtonText: 'Simpan alasan',
          cancelButtonText: 'Batal',
          confirmButtonColor: '#dc3545',
          inputValidator: (value) => !value || !value.trim() ? 'Alasan kegagalan wajib diisi.' : undefined
        });
        if (!result.isConfirmed) return;
        makeForm(bulkRejectUrl, {
          stage,
          rejection_reason: result.value.trim(),
          'candidate_ids[]': candidateIds
        }).appendTo('body').trigger('submit');
      });

      $('#bulk-delete-failed-candidates').on('click', async function () {
        const candidateIds = Array.from(selectedCandidateIds);
        if (candidateIds.length === 0) return;

        const result = await careerAlert.fire({
          icon: 'warning',
          title: 'Hapus kandidat gagal terpilih?',
          text: `${candidateIds.length} data kandidat akan dihapus permanen dan tidak dapat dipulihkan.`,
          showCancelButton: true,
          confirmButtonText: 'Ya, hapus permanen',
          cancelButtonText: 'Batal',
          confirmButtonColor: '#dc3545'
        });
        if (!result.isConfirmed) return;
        makeForm(bulkDeleteUrl, { 'candidate_ids[]': candidateIds }, 'DELETE').appendTo('body').trigger('submit');
      });

      $('#career-candidates-table').on('click', '.js-view-cv', function () {
        const cvUrl = $(this).data('url');
        careerAlert.fire({
          title: 'Curriculum Vitae',
          html: `<iframe src="${escapeHtml(cvUrl)}" title="Curriculum Vitae kandidat" class="w-100 h-100 border-0"></iframe>`,
          width: '1000px',
          heightAuto: false,
          customClass: {
            popup: 'career-cv-popup',
            htmlContainer: 'career-cv-content'
          },
          showCloseButton: true,
          showConfirmButton: false,
          footer: `<a href="${escapeHtml(cvUrl)}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary"><i class="fas fa-external-link-alt"></i> Buka di tab baru</a>`,
          didOpen: () => {
            const popup = careerAlert.getPopup();
            const content = careerAlert.getHtmlContainer();
            const viewer = content.querySelector('iframe');

            popup.style.display = 'flex';
            popup.style.flexDirection = 'column';
            popup.style.height = `${Math.max(460, Math.floor(window.innerHeight * 0.92))}px`;
            const viewerHeight = Math.max(320, popup.clientHeight - 145);
            content.style.flex = '1 1 auto';
            content.style.height = `${viewerHeight}px`;
            content.style.maxHeight = 'none';
            content.style.overflow = 'hidden';
            viewer.style.height = `${viewerHeight}px`;
          }
        });
      });

      $('#career-candidates-table').on('click', '.js-delete-failed', async function () {
        const result = await careerAlert.fire({
          icon: 'warning',
          title: 'Hapus permanen kandidat?',
          text: 'Data kandidat gagal ini tidak dapat dipulihkan.',
          showCancelButton: true,
          confirmButtonText: 'Ya, hapus permanen',
          cancelButtonText: 'Batal',
          confirmButtonColor: '#dc3545'
        });
        if (!result.isConfirmed) return;

        const form = $('<form>', { method: 'POST', action: $(this).data('url') });
        form.append($('<input>', { type: 'hidden', name: '_token', value: csrfToken }));
        form.append($('<input>', { type: 'hidden', name: '_method', value: 'DELETE' }));
        form.appendTo('body').trigger('submit');
      });

      $('#career-candidates-table').on('click', '.js-restore-failed', async function () {
        const button = this;
        const restoreUrl = button.dataset.url;
        const hasCv = button.dataset.hasCv === '1';
        const needsPosition = button.dataset.needsPosition === '1';
        const restoreFields = [
          needsPosition
            ? '<div class="text-start mb-3"><label for="restore-job-title" class="form-label">Posisi kandidat</label><input id="restore-job-title" class="form-control" maxlength="160" value="TAX OFFICER" required></div>'
            : '',
          hasCv
            ? '<div class="alert alert-info text-start mb-0">CV tersimpan dan akan digunakan kembali.</div>'
            : '<div class="alert alert-warning text-start mb-0">CV tidak ditemukan di penyimpanan. Kandidat tetap dipulihkan tanpa CV.</div>'
        ].join('');

        const result = await careerAlert.fire({
          icon: 'question',
          title: 'Pulihkan ke Screening CV?',
          html: `<div class="text-start"><p>Kandidat akan dikembalikan ke tahap Screening CV.</p>${restoreFields}</div>`,
          showCancelButton: true,
          confirmButtonText: 'Pulihkan kandidat',
          cancelButtonText: 'Batal',
          confirmButtonColor: '#198754',
          showLoaderOnConfirm: true,
          preConfirm: async () => {
            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('_method', 'PUT');

            if (needsPosition) {
              const jobTitle = document.getElementById('restore-job-title').value.trim();
              if (!jobTitle) {
                careerAlert.showValidationMessage('Posisi kandidat wajib diisi.');
                return false;
              }
              formData.append('job_title', jobTitle);
            }

            try {
              const response = await fetch(restoreUrl, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
              });

              if (response.status === 422) {
                const payload = await response.json();
                const errors = Object.values(payload.errors || {}).flat();
                careerAlert.showValidationMessage(errors.join(' ') || payload.message || 'Periksa data CV dan posisi kandidat.');
                return false;
              }
              if (!response.ok) {
                throw new Error(`Permintaan gagal (HTTP ${response.status}).`);
              }
              if (response.redirected && !new URL(response.url).pathname.includes('/career-candidates/failed')) {
                throw new Error('Sesi admin berakhir. Silakan masuk kembali lalu coba lagi.');
              }

              return true;
            } catch (error) {
              careerAlert.showValidationMessage(error.message || 'Kandidat gagal dipulihkan.');
              return false;
            }
          }
        });

        if (result.isConfirmed) {
          selectedCandidateIds.delete(String(button.dataset.id));
          candidateTable.ajax.reload(null, false);
          await careerAlert.fire({
            icon: 'success',
            title: 'Kandidat dipulihkan',
            text: 'Kandidat sekarang berada pada tahap Screening CV.',
            timer: 1800,
            showConfirmButton: false
          });
        }
      });

      $('#career-candidates-table').on('click', '.js-advance', async function () {
        const result = await careerAlert.fire({
          icon: 'question',
          title: 'Lanjutkan kandidat?',
          text: `Kandidat akan dipindahkan ke ${nextStageLabel}.`,
          showCancelButton: true,
          confirmButtonText: 'Ya, lanjutkan',
          cancelButtonText: 'Batal',
          confirmButtonColor: '#16a34a'
        });
        if (!result.isConfirmed) return;
        makeForm($(this).data('url'), {}).appendTo('body').trigger('submit');
      });

      $('#career-candidates-table').on('click', '.js-talent-pool', async function () {
        const result = await careerAlert.fire({
          icon: 'info',
          title: 'Simpan ke Talent Pool',
          text: 'Tambahkan alasan agar kandidat dapat dipertimbangkan pada rekrutmen berikutnya.',
          input: 'textarea',
          inputPlaceholder: 'Contoh: Kompetensi teknis baik, belum sesuai kebutuhan posisi saat ini.',
          inputAttributes: {
            'aria-label': 'Alasan Talent Pool',
            maxlength: 3000
          },
          showCancelButton: true,
          confirmButtonText: 'Simpan ke Talent Pool',
          cancelButtonText: 'Batal',
          confirmButtonColor: '#f26b21',
          inputValidator: (value) => !value || !value.trim() ? 'Alasan Talent Pool wajib diisi.' : undefined
        });
        if (!result.isConfirmed) return;
        makeForm($(this).data('url'), { talent_pool_notes: result.value.trim() }).appendTo('body').trigger('submit');
      });

      $('#career-candidates-table').on('click', '.js-reject', async function () {
        const result = await careerAlert.fire({
          icon: 'warning',
          title: 'Tandai kandidat gagal?',
          text: 'CV dan data kandidat tetap disimpan agar kandidat dapat dipulihkan.',
          input: 'textarea',
          inputPlaceholder: 'Masukkan alasan kandidat tidak lolos.',
          inputAttributes: {
            'aria-label': 'Alasan kandidat gagal',
            maxlength: 3000
          },
          showCancelButton: true,
          confirmButtonText: 'Simpan alasan',
          cancelButtonText: 'Batal',
          confirmButtonColor: '#dc3545',
          inputValidator: (value) => !value || !value.trim() ? 'Alasan kegagalan wajib diisi.' : undefined
        });
        if (!result.isConfirmed) return;
        makeForm($(this).data('url'), { rejection_reason: result.value.trim() }).appendTo('body').trigger('submit');
      });
    });
  </script>
@endsection