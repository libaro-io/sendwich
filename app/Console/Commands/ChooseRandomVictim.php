<?php

namespace App\Console\Commands;

use App\Actions\ChooseRunner;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ChooseRandomVictim extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ChooseRandomVictim';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Searching an innocent victim and force him to get some sandwiches';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $currentTime = now()->format('H:i:s');
        $companies = Company::query()->where('select_runner_at', '=', $currentTime)->whereHas('orders', function ($query) {
            $query->whereDate('date', Carbon::today());
        })->get();

        foreach ($companies as $company) {
            $action = new ChooseRunner($company);
            $action->execute();
        }
    }

}
