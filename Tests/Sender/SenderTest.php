<?php

namespace Alawar\NginxPushStreamBundle\Tests\Sender;

use Alawar\NginxPushStreamBundle\Http\Sender;

class SenderTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $sender = new Sender();
        $this->assertInstanceOf("Alawar\\NginxPushStreamBundle\\Http\\SenderInterface", $sender);
    }

}
