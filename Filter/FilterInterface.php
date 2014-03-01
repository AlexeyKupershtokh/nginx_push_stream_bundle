<?php


// @codeCoverageIgnoreStart

namespace Alawar\NginxPushStreamBundle\Filter;

interface FilterInterface
{
    /**
     * @param $data string
     * @return string
     */
    public function filter($data);
}
