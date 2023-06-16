<?php

namespace App\Http\Controllers;

class LegalController
{
    public function privacy()
    {
        return inertia('Legal/Privacy');
    }

    public function cookies()
    {
        return inertia('Legal/Cookies');
    }
    public function general()
    {
        return inertia('Legal/General');
    }
}
