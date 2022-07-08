<?php

namespace App\Http\Controllers;

use App\Actions\UsersWithDept;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function index()
    {
        $deptAction = new UsersWithDept();
        $users = $deptAction->execute();

        return response()->json(['users' => $users]);
    }
}
