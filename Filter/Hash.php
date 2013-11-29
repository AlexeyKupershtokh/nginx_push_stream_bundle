<?php

namespace Alawar\NginxPushStreamBundle\Filter;

class Hash implements FilterInterface
{
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function filter($data)
    {
        return hash_hmac('md5', $data, $this->config['secret']);
    }
}
