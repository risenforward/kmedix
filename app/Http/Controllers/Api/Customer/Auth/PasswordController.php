<?php

namespace App\Http\Controllers\Api\Customer\Auth;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PasswordController extends Controller
{
    use \ApiResponseTrait;

    public function reset(Request $request)
    {
        $response = $this->response;

        $validator = \Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
            'new_password' => 'required|confirmed|min:6',
        ]);

        try {
            if ($validator->fails()) {
                $response['meta'] = $this->getValidatorErrorMeta($validator);
            } else {
                $data = $request->all();

                if (\Auth::attempt($data)) {
                    $user = \Auth::user();
                    $user->password = bcrypt($data['new_password']);
                    $user->save();
                } else {
                    $response['meta'] = $this->makeErrorMeta(400, 'Current password is wrong.');
                }
            }
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }
}
