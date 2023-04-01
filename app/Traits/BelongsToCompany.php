<?php

namespace App\Traits;

use App\Models\Company;
use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany()
    {
        static::addGlobalscope(new CompanyScope());
        static::creating(function ($model) {
            if (session()->has('company_id')) {
                $model->company_id = session()->get('company_id');
            }
        });
    }

    public function company (): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
