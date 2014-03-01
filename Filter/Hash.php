<?php

namespace Alawar\NginxPushStreamBundle\Filter;

class Hash implements FilterInterface
{
    /**
     * @var array
     */
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($data)
    {
        $backtrack = "";
        if (preg_match('/\\.b\d+$/', $data, $matches)) {
            $backtrack = $matches[0];
            $data = substr($data, 0, -strlen($backtrack));
        }
        $hashResult = hash_hmac($this->config['algo'], $data, $this->config['secret']);
        return $hashResult . $backtrack;
    }
}
