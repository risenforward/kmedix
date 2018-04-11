<?php

namespace App\Jobs;

use App\AppToken;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPushNotifications extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $notification;
    protected $tokens;
    protected $appType;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notification, $tokens, $appType = 'CUSTOMER')
    {
        $this->notification = $notification;
        $this->tokens = $tokens;
        $this->appType = $appType;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pusher = \PushNotifications::getInstance();
        $pusher->config($this->appType);

        foreach($this->tokens->where('platform', (string)AppToken::IOS) as $token) {
            $pusher->iOS(['mtitle' => '', 'mdesc' => $this->notification], $token->app_token, env('PUSH_' . $this->appType . '_IOS_PRODUCTION'));
        }

        foreach($this->tokens->where('platform', (string)AppToken::ANDROID) as $token) {
            $pusher->android(['mtitle' => '', 'mdesc' => $this->notification], $token->app_token);
        }
    }
}
