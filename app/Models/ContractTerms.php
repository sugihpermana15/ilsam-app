<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractTerms extends Model
{
    protected $table = 'm_igi_contract_terms';

    protected $primaryKey = 'document_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'document_id',
        'start_date',
        'end_date',
        'renewal_type',
        'notice_period_days',
        'billing_cycle',
        'contract_value',
        'currency',
        'payment_terms',
        'scope_service',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'contract_value' => 'decimal:2',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id', 'document_id');
    }
}
