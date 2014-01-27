<?php

namespace Alawar\NginxPushStreamBundle\Http;

use Guzzle\Http\Client;

class Sender implements SenderInterface
{
    public function send($url, $body, $headers)
    {
        $client = new Client($url);
        $request = $client->post(null, $headers, $body);
        $response = $request->send();
        return $response->isSuccessful();
    }
}
