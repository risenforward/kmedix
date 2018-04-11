<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    public function prepareRules($rules, $options = [])
    {
        foreach($options as $key => $option)
        {
            $pair = explode(':', $option, 2);
            if(count($pair) > 1) {
                $rules[$key] = str_replace('{'.$pair[0].'}', $pair[1], $rules[$key]);
            } else {
                $rules[$key] = str_replace('{id}', $option, $rules[$key]);
            }
        }

        foreach($rules as $key => $rule)
        {
            $rules[$key] = preg_replace('/{.*?}/', '0', $rule);
        }

        return $rules;
    }
}
