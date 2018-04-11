<?php
namespace App\Http\Middleware;

use Closure;

class SanitizeRequestData
{
    private function trimData($input)
    {
        if (is_array($input)) {
            return array_map(function ($data) { return $this->trimData($data); }, $input);
        }

        return trim($input);
    }

    public function handle($request, Closure $next)
    {
        $input = $request->all();
        if (isset($input['phone_number']) && !empty($input['phone_number'])) {
            $input['phone_number'] = intl_phone($input['phone_number']);
        }
        if (isset($input['fax_number']) && !empty($input['fax_number'])) {
            $input['fax_number'] = intl_phone($input['fax_number']);
        }
        $request->merge(array_map(function ($data) { return $this->trimData($data); }, $input));
        return $next($request);
    }
}