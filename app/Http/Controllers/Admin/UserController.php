<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return $users;
    }
    public function store()
    {
         

       request()->validate([
            'email' => 'required|email|unique:users,email',
        ]);



        return User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
        ]);
    }
    public function update(User $user)
    {
        $user->update([
            'name' => request('name'),
            'email' => request('email'),
            'password' => request('password') ?  bcrypt(request('password')) : $user->password,
        ]);
        return $user;
    }
}
