<?php


// @codeCoverageIgnoreStart

namespace Alawar\NginxPushStreamBundle\Filter;

interface FilterInterface
{
    /**
     * @param string $data
     * @return string
     */
    public function filter($data);
}
