<?php

trait ApiResponseTrait
{
    protected $response = [
        'meta' => [
            'code' => 200
        ],
    ];

    protected function makeErrorMeta($code, $message)
    {
        return ['code' => $code, 'error_message' => $message];
    }

    protected function getValidatorErrorMeta($validator)
    {
        return ['code' => 400, 'error_message' => $validator->errors()->all()[0]];
    }

    protected function getInternalServerErrorMeta($e)
    {
        Log::error($e);
        return ['code' => 500, 'error_message' => 'Internal server error.'];
    }

    protected function getCredentialsErrorMeta()
    {
        return ['code' => 404, 'error_message' => 'These credentials do not match our records.'];
    }

    protected function response($response)
    {
        return response()->json($response, $response['meta']['code']);
    }
}