<?php

namespace Alawar\NginxPushStreamBundle\IdGenerator;

class IdGenerator implements IdGeneratorInterface
{
    public function generate()
    {
        return uniqid(mt_rand(), true);
    }
}
