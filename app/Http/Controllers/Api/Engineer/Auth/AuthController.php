<?php

namespace App\Http\Controllers\Api\Engineer\Auth;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    use \ApiResponseTrait;

    public function login(Request $request)
    {
        $response = $this->response;

        $validator = \Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            if ($validator->fails()) {
                $response['meta'] = $this->getValidatorErrorMeta($validator);
            } else {
                $data = $request->all();

                if (\Auth::attempt($data)) {
                    $user = \Auth::user();

                    if ($user->active) {
                        $response['data'] = [
                            'id' => $user->id,
                            'email' => $user->email,
                            'name' => $user->first_name,
                            'role' => $user->role_name,
                            'api_token' => $user->api_token,
                        ];
                    } else {
                        $response['meta'] = $this->makeErrorMeta(401, 'User is deactivated.');
                    }
                } else {
                    $response['meta'] = $this->getCredentialsErrorMeta();
                }
            }
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }
}
