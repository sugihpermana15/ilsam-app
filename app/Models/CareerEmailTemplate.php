<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareerEmailTemplate extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'subject',
    'body',
  ];
}
