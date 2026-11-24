<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerProfile extends Model
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'gstin',
        'pan',
        'customer_type',
        'credit_limit',
    ];

    protected function casts(): array
    {
        // Money stays a decimal string; never cast to float.
        return [
            'credit_limit' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
