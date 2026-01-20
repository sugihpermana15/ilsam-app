<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedUser extends Model
{
  use HasFactory;

  protected $table = 'deleted_users';

  protected $fillable = [
    'user_id',
    'name',
    'username',
    'email',
    'role',
    'deleted_at',
    'deleted_by',
  ];

  public $timestamps = true;
}
