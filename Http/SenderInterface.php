<?php

namespace Alawar\NginxPushStreamBundle\Http;

interface SenderInterface
{
    public function send($url, $body, $headers);
}
