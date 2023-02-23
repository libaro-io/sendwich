<?php

namespace App\Http\Controllers;

use App\Actions\UsersWithDept;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{

    public function index()
    {
        $deptAction = new UsersWithDept();
        $users = $deptAction->execute();

        return response()->json(['users' => $users]);
    }

    public function destroy($id){
        $user = User::findOrFail($id);

        if( UsersWithDept::calculateUserDept($user) > 0.25 ){
            return Redirect::back()->with(['error'=>'User cannot be deleted !']);
        }

        //$user->delete();
        return Redirect::back()->with(['success'=>'User is successfully deleted !']);

    }

}
