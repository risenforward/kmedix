<?php

namespace App\Http\Controllers;

use App\Supplier;
use Illuminate\Http\Request;

use App\Http\Requests;

class SupplierController extends Controller
{
    use \ImageTrait;

    private static $createdMsg = 'Supplier successfully created';
    private static $updatedMsg = 'Supplier successfully updated';
    private static $deletedMsg = 'Supplier successfully deactivated';

    public function index()
    {
        return view('supplier.index', [
            'suppliers' => Supplier::all()->load('contactPersons', 'deviceModels')
        ]);
    }

    public function create(Request $request)
    {
        if ($request->method() == 'POST') {
            $this->validate($request, Supplier::$rules);
            $data = $request->all();

            $supplier = Supplier::prepareSupplier($data);
            $supplier->save();
            if ($request->hasFile('image')) {
                $this->createModelImage($request->file('image'), $supplier, 'suppliers');
            }

            return redirect('/suppliers')->with([
                'alert' => ['code' => 200, 'text' => self::$createdMsg]
            ]);
        } else {
            return view('supplier.create');
        }
    }

    public function update($id, Request $request)
    {
        $supplier = Supplier::find($id);
        if ($request->method() == 'PUT') {
            $this->validate($request, Supplier::$rules);
            $data = $request->all();

            $supplier = Supplier::prepareSupplier($data, $supplier);
            if ($request->hasFile('image')) {
                $supplier = $this->updateModelImage($request->file('image'), $supplier, 'suppliers');
            }
            $supplier->save();

            return redirect('/suppliers')->with([
                'alert' => ['code' => 200, 'text' => self::$updatedMsg]
            ]);
        } else {
            return view('supplier.update', [
                'supplier' => $supplier
            ]);
        }
    }

    public function deleteLogo($id, Request $request)
    {
        return $this->deleteModelLogo(Supplier::class, $id, 'suppliers');
    }

    public function delete($id)
    {
        $supplier = Supplier::find($id);
        $supplier->active = 0;
        $supplier->save();

        return redirect('/suppliers')->with([
            'alert' => ['code' => 200, 'text' => self::$deletedMsg]
        ]);
    }

    public function details($id)
    {
        return view('supplier.details', [
            'supplier' => Supplier::with('contactPersons')->find($id)
        ]);
    }
}
