<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UniformEntitlement extends Model
{
    protected $table = 'm_igi_uniform_entitlements';

    protected $fillable = [
        'employee_id',
        'uniform_id',
        'total_qty',
        'used_qty',
        'effective_from',
        'effective_to',
    ];

    protected $casts = [
        'total_qty' => 'int',
        'used_qty' => 'int',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function uniform(): BelongsTo
    {
        return $this->belongsTo(Uniform::class, 'uniform_id');
    }

    public function getRemainingQtyAttribute(): int
    {
        $total = (int) ($this->total_qty ?? 0);
        $used = (int) ($this->used_qty ?? 0);

        return max(0, $total - $used);
    }
}
