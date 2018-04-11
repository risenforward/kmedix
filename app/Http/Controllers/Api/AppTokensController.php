<?php

namespace App\Http\Controllers\Api;

use App\AppToken;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AppTokensController extends Controller
{
    use \ApiResponseTrait;

    protected $user;

    public function __construct()
    {
        $this->user = \Auth::guard('api')->user();
    }

    public function store(Request $request)
    {
        $response = $this->response;

        $validator = \Validator::make($request->all(), AppToken::$rules);

        try {
            if ($validator->fails()) {
                $response['meta'] = $this->getValidatorErrorMeta($validator);
            } else {
                $data = $request->all();

                $token = AppToken::where(function ($query) use ($data) {
                    $query->where('app_token', $data['app_token']);
                    $query->where('platform', $data['platform']);
                    $query->where('user_id', $this->user->id);
                })->first();
                if (is_null($token)) {
                    $token = new AppToken($data);
                    $this->user->appTokens()->save($token);
                }

                $response['data'] = [
                    'id' => $token->id,
                    'app_token' => $token->app_token,
                    'platform' => $token->platform,
                ];
            }
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }

    public function delete(Request $request)
    {
        $response = $this->response;

        $validator = \Validator::make($request->all(), AppToken::$rules);

        try {
            if ($validator->fails()) {
                $response['meta'] = $this->getValidatorErrorMeta($validator);
            } else {
                $data = $request->all();

                $token = AppToken::where(function ($query) use ($data) {
                    $query->where('app_token', $data['app_token']);
                    $query->where('platform', $data['platform']);
                    $query->where('user_id', $this->user->id);
                })->first();
                if (!is_null($token)) {
                    $token->delete();
                    $response['data'] = [];
                } else {
                    $response['meta'] = $this->makeErrorMeta(404, 'Token not found.');
                }
            }
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }
}
