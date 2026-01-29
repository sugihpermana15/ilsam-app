<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRetentionPolicy extends Model
{
    protected $table = 'm_igi_document_retention_policies';

    protected $fillable = [
        'document_type',
        'retention_years',
        'auto_archive',
        'auto_delete',
    ];

    protected function casts(): array
    {
        return [
            'retention_years' => 'int',
            'auto_archive' => 'bool',
            'auto_delete' => 'bool',
        ];
    }
}
