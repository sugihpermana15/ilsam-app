<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
  protected $fillable = [
    'chemical_name',
    'supplier',
    'certification_type',
    'certificate_no',
    'issued_date',
    'expiry_date',
    'scope',
    'zdhc_link',
    'proof_path',
  ];

  protected $casts = [
    'issued_date' => 'date',
    'expiry_date' => 'date',
  ];
}
