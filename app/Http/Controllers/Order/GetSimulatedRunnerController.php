<?php

namespace App\Http\Controllers\Order;

use App\Actions\ChooseRunner;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetSimulatedRunnerController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if (Auth::check()) {
            $company = Auth::user()->company;
        } else {
            $company = Company::query()->where('token', '=', $request->input('company_token'))->firstOrFail();
        }

        $runner = (new ChooseRunner($company))->getSimulatedRunner();

        return response()->json(['runner' => $runner]);
    }
}