<?php

namespace Alawar\NginxPushStreamBundle\Tests\Sender;

use Alawar\NginxPushStreamBundle\Http\Sender;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class SenderTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $sender = new Sender();
        $this->assertInstanceOf("Alawar\\NginxPushStreamBundle\\Http\\SenderInterface", $sender);
    }

    public function testSend()
    {
        $url = 'http://localhost/pub?id=123';
        $body = '{"token":"123"}' . "\r\n";
        $headers = array(
            'Event-ID'     => '123',
            'Event-Type'   => 'new_message',
            'Content-Type' => 'application/json'
        );

        $mockResponse = $this->getMock('Guzzle\Http\Message\Response', array(), array('200'));
        $mockResponse->expects($this->once())->method('isSuccessful')->will($this->returnValue(true));

        $mockRequest = $this->getMock('Guzzle\Http\Message\Request', array(), array('post', $url));
        $mockRequest->expects($this->once())->method('send')->will($this->returnValue($mockResponse));

        $mock = $this->getMock('Guzzle\Http\Client');
        $mock->expects($this->once())->method('post')->with($url, $headers, $body)->will($this->returnValue($mockRequest));

        $sender = new Sender();
        $sender->setClient($mock);
        $result = $sender->send($url, $body, $headers);
        $this->assertEquals(true, $result);
    }
}
