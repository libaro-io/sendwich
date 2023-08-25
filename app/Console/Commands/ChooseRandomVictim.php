<?php

namespace App\Console\Commands;

use App\Actions\ChooseRunner;
use App\Models\Company;
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
        foreach (Company::query()->get() as $company) {

            $action = new ChooseRunner($company);
            $action->execute();

        }
    }

}
