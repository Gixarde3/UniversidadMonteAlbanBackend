<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function registerUser(Request $request){
        try {
            $admin = User::where('cookie',$request->cookie)->where('role','>=', 3)->first();
            if ($admin) {
                $this->validate($request, [
                    'username' => 'required|unique:users',
                    'email' => 'required|email|unique:users'
                ]);
                $newUser = new User();
                $newUser->username = $request->username;
                $newUser->password = $request->password;
                $newUser->email = $request->email;
                $newUser->name = $request->name;
                $newUser->lastName = $request->lastName;
                $newUser->birthdate = $request->birthday;
                $newUser->role = 2;
                $cookie = Str::random(40);
                $newUser->cookie = $cookie;
                $newUser->save();
                $success = true;
            }else{
                $success = false;
            }
        }catch (ValidationException $e) {
            $success = false;
        }
        return response()->json([
            'success'=> $success,
        ]);
    }

    public function isAdmin(Request $request){
        $user = User::where('cookie',$request->cookie)->first();
        if ($user) {
            if ($user->role >= 3) {
                $success = true;
            } else {
                $success = false;
            }
        } else {
            $success = false;
        }
        return response()->json([
            'success'=> $success,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function searchUser(Request $request){
        $admin = User::where('cookie',$request->cookie)->where('role','>=', 3)->first();
        $usersFound = [];
        if ($admin) {
            $usersFound = User::select('id','username','role', 'name', 'lastName', 'email', 'birthdate AS birthDay')
            ->where('username','LIKE',"%$request->username%")
            ->get()
            ->toArray();
            $success = true;
        }else{
            $success = false;
        }
        return response()->json([
            'users' => $usersFound,
            'success' => $success,
        ]);
    }
    public function deleteUser(Request $request, $id_user){
        $user = User::find($id_user);
        $admin = User::where('cookie',$request->cookie)->first();
        if ($admin) {
            if ($admin->role == 3) {
                $user->delete();
                $success = true;
            }else{
                $success = false;
            }
        }else{
            $success = false;
        }
        return response()->json([
            'success'=> $success
        ]);
    }

    public function changeRole(Request $request, $id_user){
        $user = User::find($id_user);
        $admin = User::where('cookie',$request->cookie)->first();
        if ($admin) {
            if ($admin->role == 3) {
                $user->role = $request->role;
                $user->save();
                $success = true;
            }else {
                $success = false;
            }
        }else{
            $success = false;
        }
        return response()->json([
            'success'=> $success,
        ]);
    }

    public function changePassword(Request $request){
        $user = User::where('cookie',$request->cookie)->first();
        if ($user) {
            if (Hash::check($request->lastPassword, $user->password) && (!(Hash::check($request->password, $user->password)))) {
                $user->password = $request->password;
                $cookie = Str::random(40);
                $user->cookie = $cookie;
                $user->save();
                $success = true;
            } else {
                $cookie = '';
                $success = false;
            }
        } else {
            $cookie = '';
            $success = false;
        }
        return response()->json([
            'success' => $success,
            'cookie' => $cookie,
        ]);
    }

    public function searchByCookie(Request $request, $cookie){
        $user = User::where('cookie',$cookie)->first();
        if ($user) {
            return response()->json($user->toArray());
        } else {
            return response()->json([
                'success' => $success=false,
            ]);
        }
    }

    public function editByCookie(Request $request, $cookie){
        $user = User::where('cookie',$cookie)->first();
        if ($user) {
            $user->username = $request->username;
            $user->email = $request->email;
            $user->name = $request->name;
            $user->lastName = $request->lastnames;
            $user->birthdate = $request->birthday;
            if ($user->save()) {
                $success=true;
            } else {
                $success=false;
            }
        } else {
            $success=false;
        }
        return response()->json([
            'success' => $success,
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
