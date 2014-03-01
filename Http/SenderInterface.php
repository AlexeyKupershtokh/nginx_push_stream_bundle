<?php


// @codeCoverageIgnoreStart

namespace Alawar\NginxPushStreamBundle\Http;

interface SenderInterface
{
    /**
     * @param string $url
     * @param string $body
     * @param string[] $headers
     * @return mixed
     */
    public function send($url, $body, $headers);
}
