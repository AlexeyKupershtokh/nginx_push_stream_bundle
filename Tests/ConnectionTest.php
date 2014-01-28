<?php

namespace Alawar\NginxPushStreamBundle\Tests\Connection;

use Alawar\NginxPushStreamBundle\Connection;
use Alawar\NginxPushStreamBundle\Filter\Prefix;
use Alawar\NginxPushStreamBundle\Filter\Hash;
use Alawar\NginxPushStreamBundle\IdGenerator\IdGenerator;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    public $pubUrl = 'http://localhost/pub?id={token}';

    public $subUrls = array(
        'polling' => 'http://localhost/sub-p/{tokens}',
        'long-polling' => 'http://localhost/sub-lp/{tokens}',
    );

    public function testFilterPrefix()
    {
        $c = new Connection($this->pubUrl, $this->subUrls);
        $c->addFilter(new Prefix(array('prefix' => 'pref_')));
        $this->assertEquals('pref_data', $c->filter('data'));
    }

    public function testFilterHash()
    {
        $c = new Connection($this->pubUrl, $this->subUrls);
        $c->addFilter(new Hash(array('algo' => 'md5', 'secret' => 'x')));
        $this->assertEquals('6570067ba4b07c4bd953bfc37ee6b48b', $c->filter('data'));
    }

    public function testFilterHashPrefix()
    {
        $c = new Connection($this->pubUrl, $this->subUrls);
        $c->addFilter(new Hash(array('algo' => 'md5', 'secret' => 'x')));
        $c->addFilter(new Prefix(array('prefix' => 'pref_')));
        $this->assertEquals('pref_6570067ba4b07c4bd953bfc37ee6b48b', $c->filter('data'));
    }

    public function testFilterTokens()
    {
        $c = new Connection($this->pubUrl, $this->subUrls);
        $c->addFilter(new Hash(array('algo' => 'md5', 'secret' => 'x')));
        $c->addFilter(new Prefix(array('prefix' => 'pref_')));
        $this->assertEquals(
            array(
                'pref_f79ec0211abf003e5fb031bfb66a6e6b',
                'pref_1b62a7244c5d2b17fe4f03ba864d0911'
            ),
            $c->filterTokens(array('data1', 'data2'))
        );
    }

    public function testPubUrl()
    {
        $c = new Connection($this->pubUrl, $this->subUrls);
        $value = $c->getPubUrl('token1');
        $this->assertEquals('http://localhost/pub?id=token1', $value);
    }

    public function testPubUrlWFilters()
    {
        $c = new Connection($this->pubUrl, $this->subUrls);
        $c->addFilter(new Prefix(array('prefix' => 'pref_')));
        $value = $c->getPubUrl('token1');
        $this->assertEquals('http://localhost/pub?id=pref_token1', $value);
    }

    public function testSubUrls()
    {
        $c = new Connection($this->pubUrl, $this->subUrls);
        $value = $c->getSubUrls(array('token1', 'token2'));
        $this->assertEquals(
            array(
                'polling' => 'http://localhost/sub-p/token1/token2',
                'long-polling' => 'http://localhost/sub-lp/token1/token2'
            ),
            $value
        );
    }

    public function testSubUrlsWFilters()
    {
        $c = new Connection($this->pubUrl, $this->subUrls);
        $c->addFilter(new Prefix(array('prefix' => 'pref_')));
        $value = $c->getSubUrls(array('token1', 'token2'));
        $this->assertEquals(
            array(
                'polling' => 'http://localhost/sub-p/pref_token1/pref_token2',
                'long-polling' => 'http://localhost/sub-lp/pref_token1/pref_token2'
            ),
            $value
        );
    }

    public function testWoSender()
    {
        $c = new Connection($this->pubUrl, $this->subUrls);
        $return = $c->send('123', array('type' => 'message', 'from' => 's', 'text' => 'Yay!'), 'new_message', 1);
        $this->assertFalse($return);
    }

    public function testSending()
    {
        $mock = $this->getMock('Alawar\NginxPushStreamBundle\Http\Sender');
        $mock->expects($this->once())->method('send')->with(
            'http://localhost/pub?id=123',
            '{"token":"123","id":1,"type":"new_message","data":{"type":"message","from":"s","text":"Yay!"}}' . "\r\n",
            array(
                'Event-ID' => '1',
                'Event-Type' => 'new_message',
                'Content-Type' => 'application/json'
            )
        );

        $c = new Connection($this->pubUrl, $this->subUrls);
        $c->setSender($mock);
        $c->send('123', array('type' => 'message', 'from' => 's', 'text' => 'Yay!'), 'new_message', 1);
    }

    public function testSendingWoId()
    {
        $mock = $this->getMock('Alawar\NginxPushStreamBundle\Http\Sender');
        $mock->expects($this->once())->method('send')->with(
            'http://localhost/pub?id=123',
            '{"token":"123","type":"new_message","data":{"type":"message","from":"s","text":"Yay!"}}' . "\r\n",
            array(
                'Event-Type' => 'new_message',
                'Content-Type' => 'application/json'
            )
        );

        $c = new Connection($this->pubUrl, $this->subUrls);
        $c->setSender($mock);
        $c->send('123', array('type' => 'message', 'from' => 's', 'text' => 'Yay!'), 'new_message');
    }

    public function testSendingWoIdButWithGenerator()
    {
        $generator = new IdGenerator();
        $id = $generator->generate();

        $generatorMock = $this->getMock('Alawar\NginxPushStreamBundle\IdGenerator\IdGenerator');
        $generatorMock->expects($this->once())->method('generate')->with()->will($this->returnValue($id));

        $mock = $this->getMock('Alawar\NginxPushStreamBundle\Http\Sender');
        $mock->expects($this->once())->method('send')->with(
            'http://localhost/pub?id=123',
            '{"token":"123","id":"' . $id . '","type":"new_message","data":{"type":"message","from":"s","text":"Yay!"}}' . "\r\n",
            array(
                'Event-ID' => $id,
                'Event-Type' => 'new_message',
                'Content-Type' => 'application/json'
            )
        );

        $c = new Connection($this->pubUrl, $this->subUrls);
        $c->setIdGenerator($generatorMock);
        $c->setSender($mock);
        $c->send('123', array('type' => 'message', 'from' => 's', 'text' => 'Yay!'), 'new_message');
    }

}
