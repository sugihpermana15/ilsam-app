<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UniformAllocation extends Model
{
    protected $table = 'm_igi_uniform_allocations';

    protected $fillable = [
        'allocation_no',
        'allocation_method',
        'allocated_at',
        'employee_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'allocated_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(UniformAllocationItem::class, 'uniform_allocation_id');
    }
}
