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
        $company->auto_assign_runner = $request->validated('auto_assign_runner');
        $company->save();

        return redirect()->action(ShowSettingsController::class);
    }
}
