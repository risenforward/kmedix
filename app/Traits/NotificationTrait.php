<?php

use Illuminate\Http\Request;

trait NotificationTrait {
    public function notification($id, Request $request)
    {
        if ($this instanceof \App\Http\Controllers\SalesRequestController) {
            $route = 'salesRequest';
            $model = \App\SalesRequest::class;
            $back = ['url' => 'salesRequests', 'name' => 'Sales requests'];
        } else {
            $route = 'complain';
            $model = \App\Complain::class;
            $back = ['url' => 'complains', 'name' => 'Complains'];
        }

        if ($request->method() == 'POST') {
            $this->validate($request, ['message' => 'required']);
            $data = $request->all();

            $job = (new \App\Jobs\SendPushNotifications(
                $data['message'],
                \App\Customer::find($data['customer'])->user->appTokens
            ))->delay(PUSH_NOTIFICATION_DELAY);

            $this->dispatch($job);

            return redirect($back['url'])->with([
                'alert' => ['code' => 200, 'text' => 'Notification is successfully sended!']
            ]);
        } else {
            return view('notification.create', [
                'back' => $back,
                'route' => $route,
                'model' => $model::find($id),
                'customers' => \App\Customer::all()->load('user')->filter(function ($customer) {
                    return $customer->user->active;
                })->transform(function ($customer) {
                    return ['id' => $customer->id, 'name' => $customer->clinic_name];
                })
            ]);
        }
    }
}