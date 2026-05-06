<?php

namespace App\Http\Controllers\Company\Settings;

use App\Enums\NotificationDriver;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class ShowSettingsController extends Controller
{
    public function __invoke(): Response
    {
        $company = Auth::user()->company;
        $company->load('notificationChannels');

        return inertia('Settings', [
            'company' => $company,
            'availableDrivers' => NotificationDriver::toArray(),
        ]);
    }
}
