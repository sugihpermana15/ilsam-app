@extends('layouts.master')

@section('title', 'Career Management | ILSAM')
@section('title-sub', 'Career')
@section('pagetitle', 'Career')

@section('css')
  <style>
    .career-pill {
      border: 1px dashed rgba(15, 23, 42, 0.25);
      background: rgba(226, 232, 240, 0.4);
      padding: 8px 12px;
      border-radius: 10px;
    }
  </style>
@endsection

@section('content')
  @php
    $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'career', 'create');
    $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'career', 'update');
    $canDelete = \App\Support\MenuAccess::can(auth()->user(), 'career', 'delete');

    $careers = collect($careers ?? []);
    $departmentOptions = collect($departmentOptions ?? $careers->pluck('department'))->filter()->unique()->sort()->values()->all();
    $locationOptions = collect($locationOptions ?? $careers->pluck('location'))->filter()->unique()->sort()->values()->all();
    $stats = $stats ?? [
      'total' => $careers->count(),
      'active' => $careers->where('is_active', true)->count(),
      'draft' => $careers->where('is_active', false)->count(),
    ];
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
        <div
          class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
          <div>
            <h5 class="card-title mb-0">Career Management</h5>
            <div class="text-muted small">Manage public career page and job openings.</div>
          </div>
          <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.career_candidates.index') }}" class="btn btn-outline-primary btn-sm">
              <i class="bi bi-people"></i> Candidates
            </a>
            <a href="{{ url('/career') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-box-arrow-up-right"></i> Preview Career Page
            </a>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCareerModal" {{ $canCreate ? '' : 'disabled' }} title="{{ $canCreate ? '' : 'Tidak punya akses tambah' }}">
              <i class="bi bi-plus-lg"></i> Add Job Opening
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-12 col-lg-4">
              <div class="career-pill d-flex align-items-center justify-content-between">
                <div class="text-muted">Total Openings</div>
                <div class="fw-semibold">{{ number_format($stats['total'] ?? 0) }}</div>
              </div>
            </div>
            <div class="col-12 col-lg-4">
              <div class="career-pill d-flex align-items-center justify-content-between">
                <div class="text-muted">Published</div>
                <div class="fw-semibold text-success">{{ number_format($stats['active'] ?? 0) }}</div>
              </div>
            </div>
            <div class="col-12 col-lg-4">
              <div class="career-pill d-flex align-items-center justify-content-between">
                <div class="text-muted">Draft</div>
                <div class="fw-semibold text-warning">{{ number_format($stats['draft'] ?? 0) }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="mb-0">Job Openings</h6>
          <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCareerModal" {{ $canCreate ? '' : 'disabled' }} title="{{ $canCreate ? '' : 'Tidak punya akses tambah' }}">
            <i class="bi bi-plus-lg"></i> Add Opening
          </button>
        </div>
        <div class="card-body table-responsive">
          @if($careers->isEmpty())
            <div class="alert alert-info mb-0">No openings yet. Add your first job opening.</div>
          @else
            <table class="table table-striped table-bordered align-middle">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Department</th>
                  <th>Location</th>
                  <th>Status</th>
                  <th>Deadline</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($careers as $career)
                  <tr>
                    <td>
                      <div class="fw-semibold">{{ $career->title ?? $career['title'] ?? '-' }}</div>
                      <div class="text-muted small">{{ $career->type ?? $career['type'] ?? 'Full-time' }} ·
                        {{ $career->work_mode ?? $career['work_mode'] ?? 'On-site' }}
                      </div>
                    </td>
                    <td>{{ $career->department ?? $career['department'] ?? '-' }}</td>
                    <td>{{ $career->location ?? $career['location'] ?? '-' }}</td>
                    <td>
                      @php
                        $isActive = (bool) ($career->is_active ?? $career['is_active'] ?? true);
                      @endphp
                      <span class="badge bg-{{ $isActive ? 'success' : 'secondary' }}">
                        {{ $isActive ? 'Published' : 'Draft' }}
                      </span>
                    </td>
                    <td>
                      @if(!empty($career->deadline ?? $career['deadline'] ?? null))
                                {{ $career->deadline instanceof \Carbon\Carbon
                        ? $career->deadline->format('d M Y')
                        : ($career['deadline'] ?? $career->deadline) }}
                      @else
                        -
                      @endif
                    </td>
                    <td>
                      <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary js-edit-career" data-bs-toggle="modal"
                          data-bs-target="#editCareerModal"
                          {{ $canUpdate ? '' : 'disabled' }} title="{{ $canUpdate ? '' : 'Tidak punya akses edit' }}"
                          data-update-url="{{ $career->update_url ?? url('/admin/careers/' . ($career->id ?? '')) }}"
                          data-title="{{ $career->title ?? $career['title'] ?? '' }}"
                          data-department="{{ $career->department ?? $career['department'] ?? '' }}"
                          data-location="{{ $career->location ?? $career['location'] ?? '' }}"
                          data-type="{{ $career->type ?? $career['type'] ?? '' }}"
                          data-work-mode="{{ $career->work_mode ?? $career['work_mode'] ?? '' }}"
                          data-experience="{{ $career->experience ?? $career['experience'] ?? '' }}"
                          data-summary="{{ $career->summary ?? $career['summary'] ?? '' }}"
                          data-requirements="{{ $career->requirements ?? $career['requirements'] ?? '' }}"
                          data-responsibilities="{{ $career->responsibilities ?? $career['responsibilities'] ?? '' }}"
                          data-apply-url="{{ $career->apply_url ?? $career['apply_url'] ?? '' }}"
                          data-deadline="{{ $career->deadline instanceof \Carbon\Carbon ? $career->deadline->format('Y-m-d') : ($career['deadline'] ?? '') }}"
                          data-is-active="{{ (int) ($career->is_active ?? $career['is_active'] ?? 1) }}">
                          <i class="bi bi-pencil"></i>
                        </button>

                        <form action="{{ $career->delete_url ?? url('/admin/careers/' . ($career->id ?? '')) }}"
                          method="POST">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline-danger" {{ $canDelete ? '' : 'disabled' }} title="{{ $canDelete ? '' : 'Tidak punya akses hapus' }}">
                            <i class="bi bi-trash"></i>
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
    </div>
  </div>

  <div class="modal fade career-modal" id="addCareerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Job Opening</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="career-create-form" action="{{ url('/admin/careers') }}" method="POST">
            @csrf
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Title</label>
                <input type="text" class="form-control" name="title" required>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Department</label>
                <select class="form-select" name="department">
                  <option value="">Select department</option>
                  @foreach(($departmentOptions ?? []) as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Location</label>
                <select class="form-select" name="location">
                  <option value="">Select location</option>
                  @foreach(($locationOptions ?? []) as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Type</label>
                <select class="form-select" name="type">
                  <option value="Full-time">Full-time</option>
                  <option value="Part-time">Part-time</option>
                  <option value="Contract">Contract</option>
                  <option value="Internship">Internship</option>
                </select>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Work Mode</label>
                <select class="form-select" name="work_mode">
                  <option value="On-site">On-site</option>
                  <option value="Hybrid">Hybrid</option>
                  <option value="Remote">Remote</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Status</label>
                <div class="d-flex flex-wrap gap-3">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="is_active" id="careerAddPublished" value="1"
                      checked>
                    <label class="form-check-label" for="careerAddPublished">Published</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="is_active" id="careerAddDraft" value="0">
                    <label class="form-check-label" for="careerAddDraft">Draft</label>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <label class="form-label">Experience</label>
                <input type="text" class="form-control" name="experience" placeholder="Example: 1-3 years">
              </div>
              <div class="col-12">
                <label class="form-label">Summary</label>
                <textarea class="form-control" rows="2" name="summary"></textarea>
              </div>
              <div class="col-12">
                <label class="form-label">Responsibilities</label>
                <textarea class="form-control" rows="3" name="responsibilities"></textarea>
              </div>
              <div class="col-12">
                <label class="form-label">Requirements</label>
                <textarea class="form-control" rows="3" name="requirements"></textarea>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Apply URL</label>
                <input type="text" class="form-control" name="apply_url" placeholder="mailto:hrd@ilsam.co.id">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Deadline</label>
                <input type="date" class="form-control" name="deadline">
              </div>
            </div>

            <div class="d-grid gap-2 d-md-none mt-3">
              <button type="submit" class="btn btn-primary" {{ $canCreate ? '' : 'disabled' }} title="{{ $canCreate ? '' : 'Tidak punya akses tambah' }}">Save Opening</button>
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" form="career-create-form" class="btn btn-primary" {{ $canCreate ? '' : 'disabled' }} title="{{ $canCreate ? '' : 'Tidak punya akses tambah' }}">Save Opening</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade career-modal" id="editCareerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Job Opening</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="career-edit-form" method="POST" action="#">
            @csrf
            @method('PUT')
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Title</label>
                <input type="text" class="form-control" name="title" required>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Department</label>
                <select class="form-select" name="department">
                  <option value="">Select department</option>
                  @foreach(($departmentOptions ?? []) as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Location</label>
                <select class="form-select" name="location">
                  <option value="">Select location</option>
                  @foreach(($locationOptions ?? []) as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Type</label>
                <select class="form-select" name="type">
                  <option value="Full-time">Full-time</option>
                  <option value="Part-time">Part-time</option>
                  <option value="Contract">Contract</option>
                  <option value="Internship">Internship</option>
                </select>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Work Mode</label>
                <select class="form-select" name="work_mode">
                  <option value="On-site">On-site</option>
                  <option value="Hybrid">Hybrid</option>
                  <option value="Remote">Remote</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Status</label>
                <div class="d-flex flex-wrap gap-3">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="is_active" id="careerEditPublished" value="1"
                      checked>
                    <label class="form-check-label" for="careerEditPublished">Published</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="is_active" id="careerEditDraft" value="0">
                    <label class="form-check-label" for="careerEditDraft">Draft</label>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <label class="form-label">Experience</label>
                <input type="text" class="form-control" name="experience">
              </div>
              <div class="col-12">
                <label class="form-label">Summary</label>
                <textarea class="form-control" rows="2" name="summary"></textarea>
              </div>
              <div class="col-12">
                <label class="form-label">Responsibilities</label>
                <textarea class="form-control" rows="3" name="responsibilities"></textarea>
              </div>
              <div class="col-12">
                <label class="form-label">Requirements</label>
                <textarea class="form-control" rows="3" name="requirements"></textarea>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Apply URL</label>
                <input type="text" class="form-control" name="apply_url">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Deadline</label>
                <input type="date" class="form-control" name="deadline">
              </div>
            </div>

            <div class="d-grid gap-2 d-md-none mt-3">
              <button type="submit" class="btn btn-primary" {{ $canUpdate ? '' : 'disabled' }} title="{{ $canUpdate ? '' : 'Tidak punya akses edit' }}">Update Opening</button>
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" form="career-edit-form" class="btn btn-primary" {{ $canUpdate ? '' : 'disabled' }} title="{{ $canUpdate ? '' : 'Tidak punya akses edit' }}">Update Opening</button>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('js')
  <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const editModal = document.getElementById('editCareerModal');
      if (!editModal) return;

      editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        if (!button) return;

        const form = document.getElementById('career-edit-form');
        if (!form) return;
        form.action = button.getAttribute('data-update-url') || '#';

        form.querySelector('[name="title"]').value = button.getAttribute('data-title') || '';
        form.querySelector('[name="department"]').value = button.getAttribute('data-department') || '';
        form.querySelector('[name="location"]').value = button.getAttribute('data-location') || '';
        form.querySelector('[name="type"]').value = button.getAttribute('data-type') || 'Full-time';
        form.querySelector('[name="work_mode"]').value = button.getAttribute('data-work-mode') || 'On-site';
        form.querySelector('[name="experience"]').value = button.getAttribute('data-experience') || '';
        form.querySelector('[name="summary"]').value = button.getAttribute('data-summary') || '';
        form.querySelector('[name="responsibilities"]').value = button.getAttribute('data-responsibilities') || '';
        form.querySelector('[name="requirements"]').value = button.getAttribute('data-requirements') || '';
        form.querySelector('[name="apply_url"]').value = button.getAttribute('data-apply-url') || '';
        form.querySelector('[name="deadline"]').value = button.getAttribute('data-deadline') || '';

        const isActive = button.getAttribute('data-is-active') === '0' ? '0' : '1';
        form.querySelectorAll('input[name="is_active"]').forEach((el) => {
          el.checked = (el.value === isActive);
        });
      });
    });
  </script>
@endsection