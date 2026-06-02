<?php

namespace App\Http\Controllers\Order;

use App\Actions\ChooseRunner;
use App\Actions\ResolveCompany;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetSimulatedRunnerController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $company = (new ResolveCompany())->execute($request);
        $runner = (new ChooseRunner($company))->getSimulatedRunner();

        return response()->json(['runner' => $runner]);
    }
}