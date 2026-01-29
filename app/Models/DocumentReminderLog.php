<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentReminderLog extends Model
{
    protected $table = 'm_igi_document_reminder_logs';

    protected $fillable = [
        'document_id',
        'days_before',
        'user_id',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'days_before' => 'int',
            'sent_at' => 'datetime',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id', 'document_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
