<?php

namespace Alawar\NginxPushStreamBundle\Tests\Filter;

use Alawar\NginxPushStreamBundle\Filter\Prefix;

class PrefixTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new Prefix(null);
        $this->assertInstanceOf("Alawar\\NginxPushStreamBundle\\Filter\\FilterInterface", $filter);
    }

    public function testMd5()
    {
        $filter = new Prefix(array('prefix' => 'pref_'));
        $value = $filter->filter('123');
        $this->assertEquals('pref_123', $value);
    }
}
