<?php

namespace Alawar\NginxPushStreamBundle\Http;

class NullSender implements SenderInterface
{
    /**
     * @param string $url
     * @param string $body
     * @param string[] $headers
     * @return bool
     */
    public function send($url, $body, $headers)
    {
    }
}
