<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserPermissionController extends Controller
{
    public function index(){
        $users = User::all();
        return view('permissions.permissions', compact('users'));
        // return view('permissions.permissions');
    }

    public function getPermissions($id){
        $user = User::findOrFail($id);

        return response()->json([
            'permissions' => $user->getPermissionNames()
        ]);
    }

    public function update(Request $request){
        $user = User::findOrFail($request->user_id);

        $user->syncPermissions($request->permissions ?? []);

        return response()->json([
            'success' => true
        ]);
    }
}
