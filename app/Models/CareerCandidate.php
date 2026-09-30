<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareerCandidate extends Model
{
  use HasFactory;

  protected $fillable = [
    'job_id',
    'job_title',
    'full_name',
    'email',
    'phone',
    'domicile',
    'linkedin_url',
    'portfolio_url',
    'message',
    'recruitment_stage',
    'selection_status',
    'stage_notes',
    'is_talent_pool',
    'talent_pool_notes',
    'processed_at',
    'cv_path',
    'cv_original_name',
    'cv_mime',
    'cv_size',
    'ip_address',
    'user_agent',
  ];

  protected function casts(): array
  {
    return [
      'cv_size' => 'integer',
      'is_talent_pool' => 'boolean',
      'processed_at' => 'datetime',
    ];
  }
}
