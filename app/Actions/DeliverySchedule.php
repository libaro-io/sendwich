<?php

namespace App\Actions;

use Carbon\Carbon;

class DeliverySchedule
{
    public function thresholdDate(): Carbon
    {
        return Carbon::now()->hour(12)->minute(15)->second(0);
    }

    public function deliveryDate(): Carbon
    {
        $date = Carbon::now()->isBefore($this->thresholdDate())
            ? Carbon::now()
            : Carbon::now()->addDay();

        return $date->hour(12)->minute(15)->second(0);
    }

    public function moment(): string
    {
        if (Carbon::now()->isBefore($this->thresholdDate())) {
            return 'today';
        }

        return 'tomorrow';
    }
}