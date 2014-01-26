<?php

namespace Alawar\NginxPushStreamBundle\Tests\IdGenerator;

use Alawar\NginxPushStreamBundle\IdGenerator\IdGenerator;

class IdGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $generator = new IdGenerator();
        $this->assertInstanceOf("Alawar\\NginxPushStreamBundle\\IdGenerator\\IdGeneratorInterface", $generator);
    }

    public function testGeneration()
    {
        $generator = new IdGenerator();
        $id1 = $generator->generate();
        $id2 = $generator->generate();
        $this->assertNotEquals($id1, $id2);
    }
}
