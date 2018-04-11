<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!\Auth::user()->active) {
            $request->session()->flash('deactivated_user', 'User ' . \Auth::user()->full_name . ' is deactivated!');
            \Auth::logout();
            return redirect('/login');
        }
        return view('dashboard');
    }
}
