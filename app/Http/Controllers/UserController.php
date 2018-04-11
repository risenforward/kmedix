<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private static $userCreatedMsg = 'User successfully created';
    private static $userUpdatedMsg = 'User successfully updated';

    public function index()
    {
        return view('user.index', [
           'users' => User::all()->load('roles')->filter(function ($user) {
               return $user->roles->first()->name != 'CUSTOMER';
           })
        ]);
    }

    public function details($id)
    {
        return view('user.details', [
           'user' => User::find($id)
        ]);
    }

    public function create(Request $request)
    {
        if ($request->method() == 'POST') {
            $this->validate($request, User::$rules);

            $data = $request->all();
            $user = User::prepareUser($data);

            $role = Role::find($data['role']);

            DB::transaction(function () use ($user, $role) {
                $user->save();
                $user->attachRole($role);
            });

            $password = $data['password'];
            Mail::send('emails.user-create', ['user' => $user, 'password' => $password],
                function ($message) use ($user, $password) {
                    $message->to($user->email)->subject('User register information');
                });

            return redirect('/users')->with([
                'alert' => ['code' => 200, 'text' => self::$userCreatedMsg]
            ]);
        } else {
            return view('user.create', [
                'roles' => Role::where('name', '<>', 'CUSTOMER')->get()
            ]);
        }
    }

    public function update($id, Request $request)
    {
        $user = User::find($id);
        if ($request->method() == 'PUT') {
            $rules = User::$rules;
            unset($rules['password']);
            $this->validate($request, $this->prepareRules($rules, [
                'email' => $user->id,
            ]));

            $data = $request->all();
            $user = User::prepareUser($data, $user);

            $role = Role::find($data['role']);

            DB::transaction(function () use ($user, $role) {
                $user->save();
                if ($role->id != $user->role_id) {
                    $user->roles()->detach($user->role_id);
                    $user->attachRole($role);
                }
            });

            return redirect('/users')->with([
                'alert' => ['code' => 200, 'text' => self::$userUpdatedMsg]
            ]);
        } else {
            return view('user.update', [
                'user' => $user,
                'roles' => Role::where('name', '<>', 'CUSTOMER')->get()
            ]);
        }
    }

    public function active($id)
    {
        $user = User::find($id);
        $user->active = !$user->active;
        $user->save();

        return redirect('/user/' . $user->id . '/details')->with([
            'alert' => ['code' => 200, 'text' => self::$userUpdatedMsg]
        ]);
    }

    public function password($id, Request $request)
    {
        $user = User::find($id);
        if ($request->method() == 'PUT') {
            $data = $request->all();
            $validator = Validator::make($data, [
                'password' => 'required|confirmed|min:6',
            ]);

            if ($validator->fails()) {
                return redirect('/user/' . $user->id . '/password')
                    ->withErrors($validator);
            }

            $user->password = bcrypt($data['password']);
            $user->save();

            return redirect('/user/' . $user->id . '/details')->with([
                'alert' => ['code' => 200, 'text' => self::$userUpdatedMsg]
            ]);
        } else {
            return view('user.password', [
                'user' => $user
            ]);
        }
    }
}