<?php

namespace App\Http\Controllers;

use App\DeviceModel;
use App\Supplier;
use Illuminate\Http\Request;

use App\Http\Requests;

class DeviceModelController extends Controller
{
    use \ImageTrait;

    private static $createdMsg = 'Model successfully created';
    private static $updatedMsg = 'Model successfully updated';
    private static $deletedMsg = 'Model successfully deleted';

    public function index(Request $request, $id = null)
    {
        if ($id && $request->ajax()) {
            return response()->json([
                'models' => DeviceModel::where(function($query) use ($id) {
                    $query->where('supplier_id', $id);
                    $query->where('active', 1);
                })->get()
            ]);
        } elseif ($id) {
            return view('device-model.index', [
                'models' => DeviceModel::with('supplier')->where('supplier_id', $id)->get()
            ]);
        }

        return view('device-model.index', [
            'models' => DeviceModel::all()->load('supplier')
        ]);
    }

    public function create(Request $request)
    {
        if ($request->method() == 'POST') {
            $this->validate($request, DeviceModel::$rules);
            $data = $request->all();

            $model = DeviceModel::prepareDeviceModel($data);
            $model->save();
            if ($request->hasFile('image')) {
                $this->createModelImage($request->file('image'), $model, 'models', 'photo');
            }

            return redirect('/devicesModels')->with([
                'alert' => ['code' => 200, 'text' => self::$createdMsg]
            ]);
        } else {
            return view('device-model.create', [
                'suppliers' => Supplier::where('active', 1)->get()
            ]);
        }
    }

    public function update($id, Request $request)
    {
        $model = DeviceModel::find($id)->load('supplier');
        if ($request->method() == 'PUT') {
            $this->validate($request, DeviceModel::$rules);
            $data = $request->all();

            $model = DeviceModel::prepareDeviceModel($data, $model);
            if ($request->hasFile('image')) {
                $model = $this->updateModelImage($request->file('image'), $model, 'models', 'photo');
            }
            $model->save();

            return redirect('/devicesModels')->with([
                'alert' => ['code' => 200, 'text' => self::$updatedMsg]
            ]);
        } else {
            return view('device-model.update', [
                'suppliers' => Supplier::where('active', 1)->get(),
                'model' => $model
            ]);
        }
    }

    public function deletePhoto($id)
    {
        return $this->deleteModelLogo(DeviceModel::class, $id, 'models', 'photo');
    }

    public function delete($id)
    {
        $deviceModel = DeviceModel::find($id);
        $deviceModel->active = 0;
        $deviceModel->save();

        return redirect('/devicesModels')->with([
            'alert' => ['code' => 200, 'text' => self::$deletedMsg]
        ]);
    }
}
