<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniformIssueLot extends Model
{
  protected $table = 'm_igi_uniform_issue_lots';

  protected $fillable = [
    'issue_id',
    'lot_id',
    'qty',
  ];

  public function issue()
  {
    return $this->belongsTo(UniformIssue::class, 'issue_id');
  }

  public function lot()
  {
    return $this->belongsTo(UniformLot::class, 'lot_id');
  }
}
