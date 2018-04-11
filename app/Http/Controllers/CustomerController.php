<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerContactPerson;
use App\Role;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    use \ImageTrait;

    private static $createdMsg = 'Customer successfully created';
    private static $updatedMsg = 'Customer successfully updated';
    private static $deletedMsg = 'Customer successfully blocked';

    public function index()
    {
        return view('customer.index', [
            'customers' => Customer::all()->load('user', 'contactPersons')
        ]);
    }

    public function create(Request $request)
    {
        if ($request->method() == 'POST') {
            $data = $request->all();
            $this->validate($request, Customer::$rules);

            $user = User::prepareUser($data);
            $user->active = true;

            $role = Role::where('name', 'CUSTOMER')->first();

            $customer = Customer::prepareCustomer($data);

            DB::transaction(function () use ($user, $role, $customer) {
                $user->save();
                $user->attachRole($role);
                $user->customer()->save($customer);
            });

            if ($request->hasFile('image')) {
                $this->createModelImage($request->file('image'), $customer, 'customers');
            }

            /*$password = $data['password'];
            Mail::send('emails.user-create', ['user' => $user, 'password' => $password],
                function ($message) use ($user, $password) {
                    $message->to($user->email)->subject('User register information');
                });*/

            return redirect('/customers')->with([
                'alert' => ['code' => 200, 'text' => self::$createdMsg]
            ]);
        } else {
            return view('customer.create');
        }
    }

    public function update($id, Request $request)
    {
        $customer = Customer::find($id)->load('user');
        if ($request->method() == 'PUT') {
            $rules = Customer::$rules;
            unset($rules['password']);
            $this->validate($request, $this->prepareRules($rules, [
                'email' => $customer->user->id,
                'username' => $customer->user->id
            ]));

            $data = $request->all();

            $user = User::prepareUser($data, $customer->user);
            $user->active = isset($data['active']) ? 0 : 1;
            $customer = Customer::prepareCustomer($data, $customer);

            if ($request->hasFile('image')) {
                $customer = $this->updateModelImage($request->file('image'), $customer, 'customers');
                $customer->save();
            }

            DB::transaction(function () use ($user, $customer) {
                $user->save();
                $customer->save();
            });

            return redirect('/customers')->with([
                'alert' => ['code' => 200, 'text' => self::$updatedMsg]
            ]);
        } else {
            return view('customer.update', [
                'customer' => $customer
            ]);
        }
    }

    public function deleteLogo($id, Request $request)
    {
        return $this->deleteModelLogo(Customer::class, $id, 'customers');
    }

    public function details($id)
    {
        return view('customer.details', [
            'customer' => Customer::with('contactPersons', 'user')->find($id)
        ]);
    }

    public function delete($id)
    {
        $customer = Customer::with('contactPersons', 'user')->find($id);
        $customer->user->active = 0;
        $customer->user->save();

        return redirect('/customers')->with([
            'alert' => ['code' => 200, 'text' => self::$deletedMsg]
        ]);
    }

    public function active($id)
    {
        $customer = Customer::find($id)->load('user');
        $customer->user->active = !$customer->user->active;
        $customer->user->save();

        return redirect('/customer/' . $customer->id . '/details')->with([
            'alert' => ['code' => 200, 'text' => self::$updatedMsg]
        ]);
    }

    public function password($id, Request $request)
    {
        $customer = Customer::find($id)->load('user');
        if ($request->method() == 'PUT') {
            $data = $request->all();
            $validator = Validator::make($data, [
                'password' => 'required|confirmed|min:6',
            ]);

            if ($validator->fails()) {
                return redirect('/customer/' . $customer->id . '/password')
                    ->withErrors($validator);
            }

            $customer->user->password = bcrypt($data['password']);
            $customer->user->save();

            return redirect('/customer/' . $customer->id . '/details')->with([
                'alert' => ['code' => 200, 'text' => self::$updatedMsg]
            ]);
        } else {
            return view('customer.password', [
                'customer' => $customer
            ]);
        }
    }
}
