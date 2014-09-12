<?php


// @codeCoverageIgnoreStart

namespace Alawar\NginxPushStreamBundle\IdGenerator;

interface IdGeneratorInterface
{
    /**
     * @return string
     */
    public function generate();
}
