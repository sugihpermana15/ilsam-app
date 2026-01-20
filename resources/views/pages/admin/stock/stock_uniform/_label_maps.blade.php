@php
  /**
   * Shared label + badge mappers for Uniform module.
   * Keep these mappings in one place to avoid duplication across views.
   */

  $uniformMovementTypeLabels = [
    'IN' => 'Stok Masuk',
    'OUT' => 'Distribusi',
    'RETURN' => 'Retur',
    'ADJUSTMENT_IN' => 'Penyesuaian Masuk',
    'ADJUSTMENT_OUT' => 'Penyesuaian Keluar',
    'WRITE_OFF' => 'Penghapusan',
    'REPLACEMENT' => 'Penggantian',
  ];

  $uniformMovementTypeBadge = function (?string $movementType): string {
    $movementType = (string) ($movementType ?? '');

    return match ($movementType) {
      'IN', 'ADJUSTMENT_IN' => 'bg-success-subtle text-success',
      'OUT', 'ADJUSTMENT_OUT', 'WRITE_OFF', 'REPLACEMENT' => 'bg-danger-subtle text-danger',
      'RETURN' => 'bg-info-subtle text-info',
      default => 'bg-secondary-subtle text-secondary',
    };
  };

  $uniformIssueStatusLabels = [
    'ISSUED' => 'Didistribusikan',
    'RETURNED' => 'Diretur',
    'REPLACED' => 'Diganti',
    'DAMAGED' => 'Rusak',
  ];

  $uniformIssueStatusBadge = function (?string $status): string {
    $status = (string) ($status ?? '');

    return match ($status) {
      'ISSUED' => 'bg-info-subtle text-info',
      'RETURNED' => 'bg-success-subtle text-success',
      'REPLACED' => 'bg-warning-subtle text-warning',
      'DAMAGED' => 'bg-danger-subtle text-danger',
      default => 'bg-secondary-subtle text-secondary',
    };
  };

  $uniformApprovalStatusLabels = [
    'PENDING' => 'Menunggu',
    'APPROVED' => 'Disetujui',
    'REJECTED' => 'Ditolak',
  ];

  $uniformApprovalStatusBadge = function (?string $status): string {
    $status = (string) ($status ?? '');

    return match ($status) {
      'PENDING' => 'bg-warning-subtle text-warning',
      'APPROVED' => 'bg-success-subtle text-success',
      'REJECTED' => 'bg-danger-subtle text-danger',
      default => 'bg-secondary-subtle text-secondary',
    };
  };
@endphp