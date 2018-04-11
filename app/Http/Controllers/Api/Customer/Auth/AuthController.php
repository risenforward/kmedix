<?php

namespace App\Http\Controllers\Api\Customer\Auth;

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
            'username' => 'required',
            'password' => 'required'
        ]);

        try {
            if ($validator->fails()) {
                $response['meta'] = $this->getValidatorErrorMeta($validator);
            } else {
                $data = $request->all();

                if (\Auth::attempt($data)) {
                    $user = \Auth::user();

                    $response['data'] = [
                        'id' => $user->customer->id,
                        'username' => $user->username,
                        'clinic_name' => $user->customer->clinic_name,
                        'api_token' => $user->api_token,
                        'image_url' => $user->customer->logo_url,
                        'active' => $user->active
                    ];
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
