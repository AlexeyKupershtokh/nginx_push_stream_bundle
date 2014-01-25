<?php

namespace Alawar\NginxPushStreamBundle\Tests\Filter;

use Alawar\NginxPushStreamBundle\Filter\Hash;
use Alawar\NginxPushStreamBundle\Filter\FilterInterface;

class HashTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new Hash(null);
        $this->assertInstanceOf("Alawar\\NginxPushStreamBundle\\Filter\\FilterInterface", $filter);
    }

    public function testMd5()
    {
        $filter = new Hash(array('algo' => 'md5', 'secret' => 'x'));
        $value = $filter->filter('123');
        $this->assertEquals('616854ae5ec4160acf306cd5093434e7', $value);
    }

    public function testSha1()
    {
        $filter = new Hash(array('algo' => 'sha1', 'secret' => 'x'));
        $value = $filter->filter('123');
        $this->assertEquals('de3c4e333cd84943bb237793e9597a3bf8cc8bb7', $value);
    }
}
