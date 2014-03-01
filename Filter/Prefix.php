<?php

namespace Alawar\NginxPushStreamBundle\Filter;

class Prefix implements FilterInterface
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
        return $this->config['prefix'] . $data;
    }
}
