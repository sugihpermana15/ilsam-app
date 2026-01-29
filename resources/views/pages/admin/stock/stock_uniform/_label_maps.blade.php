@php
  /**
   * Shared label + badge mappers for Uniform module.
   * Keep these mappings in one place to avoid duplication across views.
   */

  $uniformMovementTypeLabels = [
    'IN' => __('uniforms.movement_types.IN'),
    'OUT' => __('uniforms.movement_types.OUT'),
    'RETURN' => __('uniforms.movement_types.RETURN'),
    'ADJUSTMENT_IN' => __('uniforms.movement_types.ADJUSTMENT_IN'),
    'ADJUSTMENT_OUT' => __('uniforms.movement_types.ADJUSTMENT_OUT'),
    'WRITE_OFF' => __('uniforms.movement_types.WRITE_OFF'),
    'REPLACEMENT' => __('uniforms.movement_types.REPLACEMENT'),
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
    'ISSUED' => __('uniforms.issue_status.ISSUED'),
    'RETURNED' => __('uniforms.issue_status.RETURNED'),
    'REPLACED' => __('uniforms.issue_status.REPLACED'),
    'DAMAGED' => __('uniforms.issue_status.DAMAGED'),
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
    'PENDING' => __('uniforms.approval_status.PENDING'),
    'APPROVED' => __('uniforms.approval_status.APPROVED'),
    'REJECTED' => __('uniforms.approval_status.REJECTED'),
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