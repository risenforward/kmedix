<?php

namespace App\Http\Controllers;

use App\Complain;
use App\Customer;
use Illuminate\Http\Request;

use App\Http\Requests;

class ComplainController extends Controller
{
    use \NotificationTrait;

    private static $createdMsg = 'Complain successfully created';
    private static $updatedMsg = 'Complain successfully updated';
    private static $deletedMsg = 'Complain successfully deleted';

    public function index()
    {
        $complains = Complain::with('customer');
        $new = clone $complains;
        return view('complain.index', [
            'complains' =>$complains->where('status', Complain::PROCESSED)->get(),
            'new' => $new
                ->where('status', Complain::NOT_PROCESSED)
                ->orderBy('created_at', 'desc')
                ->get()
        ]);
    }

    public function update($id, Request $request)
    {
        $complain = Complain::find($id);
        if ($request->method() == 'PUT') {
            $data = $request->all();
            $this->validate($request, ['notes' => 'required']);
            $complain->notes = $data['notes'];

            $complain->status = Complain::PROCESSED;
            $complain->save();

            return redirect('/complains')->with([
                'alert' => ['code' => 200, 'text' => self::$updatedMsg]
            ]);
        } else {
            return view('complain.update', [
                'complain' => $complain,
            ]);
        }
    }

    public function delete($id)
    {
        $complain = Complain::find($id);
        $complain->status = Complain::PROCESSED;
        $complain->save();
        $complain->delete();

        return redirect()->back()->with([
            'alert' => ['code' => 200, 'text' => self::$deletedMsg]
        ]);
    }
}
