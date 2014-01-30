<?php

namespace Alawar\NginxPushStreamBundle\Http;

use Guzzle\Http\Client;
use Guzzle\Http\ClientInterface;

class Sender implements SenderInterface
{
    /**
     * @var \Guzzle\Http\Client|null
     */
    protected $client = null;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param ClientInterface $client
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param $url string
     * @param $body string
     * @param $headers array
     * @return bool
     */
    public function send($url, $body, $headers)
    {
        $request = $this->client->post($url, $headers, $body);
        $response = $request->send();
        return $response->isSuccessful();
    }
}
