<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentPayment extends Model
{
    protected $table = 'm_igi_document_payments';

    protected $fillable = [
        'related_invoice_document_id',
        'paid_date',
        'paid_amount',
        'payment_method',
        'reference_number',
        'proof_document_id',
    ];

    protected function casts(): array
    {
        return [
            'paid_date' => 'date',
            'paid_amount' => 'decimal:2',
        ];
    }

    public function relatedInvoice(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'related_invoice_document_id', 'document_id');
    }

    public function proofDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'proof_document_id', 'document_id');
    }
}
