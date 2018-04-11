<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerContactPerson;
use Illuminate\Http\Request;

use App\Http\Requests;

class CustomerContactPersonController extends Controller
{
    private static $createdMsg = 'Contact person successfully created';
    private static $updatedMsg = 'Contact person successfully updated';
    private static $deletedMsg = 'Contact person successfully deleted';

    public function index($id)
    {
        return view('customer.person.index', [
            'customer' => Customer::with('contactPersons')->find($id)
        ]);
    }

    public function create($id, Request $request)
    {
        $customer = Customer::find($id);
        if ($request->method() == 'POST') {
            $this->validate($request, CustomerContactPerson::$rules);

            $data = $request->all();

            $contactPerson = new CustomerContactPerson();
            $contactPerson->fill($data);

            $customer->contactPersons()->save($contactPerson);

            return redirect('/customer/' . $customer->id . '/persons')->with([
                'alert' => ['code' => 200, 'text' => self::$createdMsg]
            ]);
        } else {
            return view('customer.person.create', [
                'customer' => $customer
            ]);
        }
    }

    public function update($customerId, $personId, Request $request)
    {
        $person = CustomerContactPerson::with('customer')->find($personId);
        if ($request->method() == 'PUT') {
            $this->validate($request, CustomerContactPerson::$rules);

            $data = $request->all();

            $person->fill($data);
            $person->save();

            return redirect('/customer/' . $person->customer->id . '/persons')->with([
                'alert' => ['code' => 200, 'text' => self::$updatedMsg]
            ]);
        } else {
            return view('customer.person.update', [
                'customer' => $person->customer,
                'person' => $person
            ]);
        }
    }

    public function delete($customerId, $personId)
    {
        $person = CustomerContactPerson::with('customer')->find($personId);
        $person->delete();

        return redirect('/customer/' . $person->customer->id . '/persons')->with([
            'alert' => ['code' => 200, 'text' => self::$deletedMsg]
        ]);
    }
}
