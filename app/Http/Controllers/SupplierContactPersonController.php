<?php

namespace App\Http\Controllers;

use App\Supplier;
use App\SupplierContactPerson;
use Illuminate\Http\Request;

use App\Http\Requests;

class SupplierContactPersonController extends Controller
{
    private static $createdMsg = 'Contact person successfully created';
    private static $updatedMsg = 'Contact person successfully updated';
    private static $deletedMsg = 'Contact person successfully deleted';

    public function index($id)
    {
        return view('supplier.person.index', [
            'supplier' => Supplier::with('contactPersons')->find($id)
        ]);
    }

    public function create($id, Request $request)
    {
        $supplier = Supplier::find($id);
        if ($request->method() == 'POST') {
            $this->validate($request, SupplierContactPerson::$rules);

            $data = $request->all();

            $contactPerson = new SupplierContactPerson();
            $contactPerson->fill($data);

            $supplier->contactPersons()->save($contactPerson);

            return redirect('/supplier/' . $supplier->id . '/persons')->with([
                'alert' => ['code' => 200, 'text' => self::$createdMsg]
            ]);
        } else {
            return view('supplier.person.create', [
                'supplier' => $supplier
            ]);
        }
    }

    public function update($supplierId, $personId, Request $request)
    {
        $person = SupplierContactPerson::with('supplier')->find($personId);
        if ($request->method() == 'PUT') {
            $this->validate($request, $this->prepareRules(SupplierContactPerson::$rules, [
                'email' => $person->id
            ]));

            $data = $request->all();

            $person->fill($data);
            $person->save();

            return redirect('/supplier/' . $person->supplier->id . '/persons')->with([
                'alert' => ['code' => 200, 'text' => self::$updatedMsg]
            ]);
        } else {
            return view('supplier.person.update', [
                'supplier' => $person->supplier,
                'person' => $person
            ]);
        }
    }

    public function delete($supplierId, $personId)
    {
        $person = SupplierContactPerson::with('supplier')->find($personId);
        $person->delete();

        return redirect('/supplier/' . $person->supplier->id . '/persons')->with([
            'alert' => ['code' => 200, 'text' => self::$deletedMsg]
        ]);
    }
}
