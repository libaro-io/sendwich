<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyReminderDay extends Model
{
    protected $fillable = ['company_id', 'day'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
