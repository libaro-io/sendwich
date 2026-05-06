<?php

namespace App\Http\Controllers\Company\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\Settings\SaveRunnerSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SaveRunnerSettingsController extends Controller
{
    public function __invoke(SaveRunnerSettingsRequest $request): RedirectResponse
    {
        $company = Auth::user()->company;
        $company->select_runner_at = $request->validated('time');
        $company->save();

        return redirect()->action(ShowSettingsController::class);
    }
}
