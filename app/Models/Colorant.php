<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colorant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'color',
        'category',
        'bg_color',
        'image1',
        'image2',
    ];
}
