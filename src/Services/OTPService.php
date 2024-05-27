<?php

namespace Msg91\Services;

use GuzzleHttp\Client;

class OTPService {
    protected $url = 'https://control.msg91.com/api/v5/widget/verifyAccessToken';

    public function verifyToken($authKey, $token) {
        $client = new Client();
        $response = $client->post($this->url, [
            'json' => [
                'authkey' => $authKey,
                'access-token' => $token,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
