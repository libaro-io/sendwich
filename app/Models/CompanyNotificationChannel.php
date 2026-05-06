<?php

namespace App\Models;

use App\Casts\NotificationChannelConfiguration;
use App\Enums\NotificationDriver;
use Illuminate\Database\Eloquent\Model;

class CompanyNotificationChannel extends Model
{
    protected $fillable = [
        'company_id',
        'driver',
        'configuration',
        'enabled',
    ];

    protected $casts = [
        'driver' => NotificationDriver::class,
        'configuration' => NotificationChannelConfiguration::class,
        'enabled' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
