<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DocumentFile extends Model
{
    protected $table = 'm_igi_document_files';

    protected $primaryKey = 'file_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'file_id',
        'document_id',
        'version_number',
        'file_name',
        'file_type',
        'file_size',
        'storage_path',
        'checksum',
        'uploaded_by',
        'uploaded_at',
        'is_latest',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
            'is_latest' => 'bool',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->file_id)) {
                $model->file_id = (string) Str::uuid();
            }
        });
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id', 'document_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
