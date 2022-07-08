<?php

namespace App\Console\Commands;

use App\Actions\ChooseRunner;
use App\Actions\ChooseRunner as ChooseRandomVictimAlias;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RunnerController;
use App\Http\Controllers\UserController;
use App\Mail\InformVictim;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

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
