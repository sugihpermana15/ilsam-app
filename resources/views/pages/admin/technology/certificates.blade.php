@extends('layouts.master')

@section('title', 'Ilsam - Certificate Management')

@section('title-sub', 'Web Pages')
@section('pagetitle', 'Certificate Management')

@section('content')
  <div id="layout-wrapper">
    <div class="row">
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Success', text: @json(session('success')), timer: 2000, showConfirmButton: false });
          @endif
          @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Error', text: @json(session('error')), timer: 2500, showConfirmButton: false });
          @endif
          @if($errors->any())
            Swal.fire({
              icon: 'error',
              title: 'Validation Error',
              html: {!! json_encode('<div style="text-align:left;">' . implode('', array_map(fn($e) => '<div>• ' . e($e) . '</div>', $errors->all())) . '</div>') !!},
            });
          @endif
            });
      </script>

      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <div>
              <h5 class="card-title mb-0">Certificate Management</h5>
              <div class="text-muted" style="font-size: 13px;">Upload proof as PDF.</div>
            </div>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCertificateModal">Add
              Certificate</button>
          </div>

          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped align-middle mb-0">
                <thead>
                  <tr>
                    <th>Chemical</th>
                    <th>Supplier</th>
                    <th>Type</th>
                    <th>Cert No</th>
                    <th>Issued</th>
                    <th>Expiry</th>
                    <th>Scope</th>
                    <th>Proof</th>
                    <th style="width: 180px;">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($certificates as $c)
                    @php
                      $proofUrl = $c->proof_path ? route('certificates.proof', $c) : null;
                    @endphp
                    <tr>
                      <td class="fw-semibold">{{ $c->chemical_name }}</td>
                      <td>{{ $c->supplier }}</td>
                      <td>{{ $c->certification_type }}</td>
                      <td>{{ $c->certificate_no }}</td>
                      <td>{{ optional($c->issued_date)->format('d M Y') }}</td>
                      <td>{{ optional($c->expiry_date)->format('d M Y') }}</td>
                      <td>{{ $c->scope }}</td>
                      <td>
                        @if($proofUrl)
                          <a href="{{ $proofUrl }}" target="_blank" rel="noopener">View PDF</a>
                        @else
                          <span class="text-muted">—</span>
                        @endif
                      </td>
                      <td>
                        <div class="d-flex gap-2">
                          <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#editCertificateModal" data-id="{{ $c->id }}"
                            data-chemical_name="{{ e($c->chemical_name) }}" data-supplier="{{ e($c->supplier) }}"
                            data-certification_type="{{ e($c->certification_type) }}"
                            data-certificate_no="{{ e($c->certificate_no) }}"
                            data-issued_date="{{ optional($c->issued_date)->format('Y-m-d') }}"
                            data-expiry_date="{{ optional($c->expiry_date)->format('Y-m-d') }}"
                            data-scope="{{ e($c->scope) }}" data-zdhc_link="{{ e($c->zdhc_link) }}"
                            data-proof_url="{{ $proofUrl }}">Edit</button>

                          <form action="{{ route('admin.certificates.destroy', $c) }}" method="POST"
                            onsubmit="return confirm('Delete this certificate?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="9" class="text-center text-muted py-4">No certificates yet.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Add Modal -->
      <div class="modal fade" id="addCertificateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <form action="{{ route('admin.certificates.store') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="modal-header">
                <h5 class="modal-title">Add Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Chemical Name</label>
                    <input class="form-control" name="chemical_name" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Supplier</label>
                    <input class="form-control" name="supplier">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Certification Type</label>
                    <input class="form-control" name="certification_type">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Certificate No.</label>
                    <input class="form-control" name="certificate_no">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Issued Date</label>
                    <input type="date" class="form-control" name="issued_date">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Expiry Date</label>
                    <input type="date" class="form-control" name="expiry_date">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Scope</label>
                    <input class="form-control" name="scope">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">ZDHC Link</label>
                    <input class="form-control" name="zdhc_link" placeholder="https://...">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Proof PDF</label>
                    <input type="file" class="form-control" name="proof_pdf" accept="application/pdf,.pdf" required>
                    <div class="text-muted mt-1" style="font-size: 12px;">Max 10MB. PDF.</div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Save</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Edit Modal -->
      <div class="modal fade" id="editCertificateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <form id="editCertificateForm" method="POST" enctype="multipart/form-data">
              @csrf
              @method('PUT')
              <div class="modal-header">
                <h5 class="modal-title">Edit Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Chemical Name</label>
                    <input class="form-control" name="chemical_name" id="edit_chemical_name" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Supplier</label>
                    <input class="form-control" name="supplier" id="edit_supplier">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Certification Type</label>
                    <input class="form-control" name="certification_type" id="edit_certification_type">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Certificate No.</label>
                    <input class="form-control" name="certificate_no" id="edit_certificate_no">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Issued Date</label>
                    <input type="date" class="form-control" name="issued_date" id="edit_issued_date">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Expiry Date</label>
                    <input type="date" class="form-control" name="expiry_date" id="edit_expiry_date">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Scope</label>
                    <input class="form-control" name="scope" id="edit_scope">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">ZDHC Link</label>
                    <input class="form-control" name="zdhc_link" id="edit_zdhc_link" placeholder="https://...">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Replace Proof PDF (optional)</label>
                    <input type="file" class="form-control" name="proof_pdf" accept="application/pdf,.pdf">
                    <div class="mt-2" id="editProofPreview" style="display:none;">
                      <div class="text-muted" style="font-size: 12px;">Current proof:</div>
                      <a id="edit_proof_link" href="#" target="_blank" rel="noopener">Open PDF</a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <script>
        (function () {
          const modal = document.getElementById('editCertificateModal');
          if (!modal) return;

          modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const id = button.getAttribute('data-id');
            const form = document.getElementById('editCertificateForm');
            if (form && id) {
              form.action = @json(url('/admin/certificates')) + '/' + id;
            }

            const setVal = (sel, val) => {
              const el = document.querySelector(sel);
              if (el) el.value = val || '';
            };

            setVal('#edit_chemical_name', button.getAttribute('data-chemical_name'));
            setVal('#edit_supplier', button.getAttribute('data-supplier'));
            setVal('#edit_certification_type', button.getAttribute('data-certification_type'));
            setVal('#edit_certificate_no', button.getAttribute('data-certificate_no'));
            setVal('#edit_issued_date', button.getAttribute('data-issued_date'));
            setVal('#edit_expiry_date', button.getAttribute('data-expiry_date'));
            setVal('#edit_scope', button.getAttribute('data-scope'));
            setVal('#edit_zdhc_link', button.getAttribute('data-zdhc_link'));

            const proofUrl = button.getAttribute('data-proof_url');
            const previewWrap = document.getElementById('editProofPreview');
            const proofLink = document.getElementById('edit_proof_link');
            if (proofUrl && previewWrap && proofLink) {
              previewWrap.style.display = '';
              proofLink.href = proofUrl;
            } else if (previewWrap) {
              previewWrap.style.display = 'none';
            }
          });
        })();
      </script>
    </div>
  </div>
@endsection