<?php

class PushNotifications
{
    private static $ACCESS_KEY;

    private static $PASSPHRASSE;
    private static $DEV_CERT_PATH;
    private static $PROD_CERT_PATH;

    private static $instance = null;

    private function __construct(){}

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __clone() {}

    public function config($key)
    {
        self::$ACCESS_KEY = env('PUSH_' . $key . '_ANDROID_ACCESS_KEY');
        self::$PASSPHRASSE = env('PUSH_' . $key . '_IOS_PASSPHRASE');
        self::$DEV_CERT_PATH = env('PUSH_' . $key . '_IOS_DEV_CERT_PATH');
        self::$PROD_CERT_PATH = env('PUSH_' . $key . '_IOS_PROD_CERT_PATH');
    }

    public function android($data, $reg_id)
    {
        $url = 'https://android.googleapis.com/gcm/send';
        $message = [
            'title' => $data['mtitle'],
            'message' => $data['mdesc'],
            'subtitle' => '',
            'tickerText' => '',
            'msgcnt' => 1,
            'vibrate' => 1,
        ];

        $headers = [
            'Authorization: key=' . self::$ACCESS_KEY,
            'Content-Type: application/json'
        ];

        $fields = [
            'registration_ids' => array($reg_id),
            'data' => $message
        ];

        return self::useCurl($url, $headers, json_encode($fields));
    }

    public function iOS($data, $deviceToken, $prod)
    {
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', self::$DEV_CERT_PATH);
        stream_context_set_option($ctx, 'ssl', 'passphrase', self::$PASSPHRASSE);

        $fp = stream_socket_client(
            'ssl://gateway.sandbox.push.apple.com:2195', $err,
            $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

        if ($prod) {
            stream_context_set_option($ctx, 'ssl', 'local_cert', self::$PROD_CERT_PATH);
            $fp = stream_socket_client(
                'ssl://gateway.push.apple.com:2195', $err,
                $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        }

        if (!$fp) {
            return "Failed to connect: $err $errstr" . PHP_EOL;
        }

        $body['aps'] = array(
            'alert' => array(
                'title' => $data['mtitle'],
                'body' => $data['mdesc'],
            ),
            'sound' => 'engine.caf'
        );

        $payload = json_encode($body);
        $msg = chr(0) . pack("n", 32) . pack("H*", $deviceToken) . pack("n", strlen($payload)) . $payload;

        $result = fwrite($fp, $msg, strlen($msg));
        fclose($fp);

        return $result;

    }

    private function useCurl($url, $headers, $fields)
    {
        $ch = curl_init();
        if ($url) {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            if ($fields) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            }

            $result = curl_exec($ch);
            if ($result === false) {
                return 'Curl failed: ' . curl_error($ch);
            }

            curl_close($ch);
            return $result;
        }
    }
}

