<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $company_id
 * @property int $payer_id
 * @property int $receiver_id
 * @property float $amount
 * @property Carbon $date
 * @property int|null $source_order_id
 */
class Settlement extends Model
{
    protected $fillable = [
        'company_id',
        'payer_id',
        'receiver_id',
        'amount',
        'date',
        'source_order_id',
    ];

    protected $casts = [
        'date'   => 'datetime',
        'amount' => 'float',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id')->withTrashed();
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id')->withTrashed();
    }

    public function sourceOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'source_order_id');
    }
}