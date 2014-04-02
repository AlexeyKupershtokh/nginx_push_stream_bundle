<?php

namespace Alawar\NginxPushStreamBundle\Tests\Connection\DependencyInjection;

use Alawar\NginxPushStreamBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessMinimalConfig()
    {
        $configs = array(
            array(
                'pub_url'  => 'http://localhost/pub?id={token}',
                'sub_urls' => array(
                    'polling'      => 'http://localhost/sub-p/{tokens}',
                    'long-polling' => 'http://localhost/sub-lp/{tokens}',
                )
            )
        );

        $config = $this->process($configs);

        $this->assertEquals(
            $config,
            array(
                'default_connection' => 'default',
                'connections'        => array(
                    'default' => array(
                        'pub_url'  => 'http://localhost/pub?id={token}',
                        'sub_urls' => array(
                            'polling'      => 'http://localhost/sub-p/{tokens}',
                            'long_polling' => 'http://localhost/sub-lp/{tokens}',
                        ),
                        'id_generator' => true,
                        'sender' => true,
                        'filters'  => array(),
                    ),
                ),
            )
        );
    }

    public function testProcessOneConnection()
    {
        $configs = array(
            array(
                'default_connection' => 'default',
                'connections'        => array(
                    'default' => array(
                        'pub_url'  => 'http://localhost/pub?id={token}',
                        'sub_urls' => array(
                            'polling'      => 'http://localhost/sub-p/{tokens}',
                            'long-polling' => 'http://localhost/sub-lp/{tokens}',
                        )
                    )
                )
            )
        );

        $config = $this->process($configs);

        $this->assertEquals(
            $config,
            array(
                'default_connection' => 'default',
                'connections'        => array(
                    'default' => array(
                        'pub_url'  => 'http://localhost/pub?id={token}',
                        'sub_urls' => array(
                            'polling'      => 'http://localhost/sub-p/{tokens}',
                            'long_polling' => 'http://localhost/sub-lp/{tokens}',
                        ),
                        'id_generator' => true,
                        'sender' => true,
                        'filters'  => array(),
                    ),
                ),
            )
        );
    }

    public function testProcessFilters()
    {
        $configs = array(
            array(
                'pub_url'  => 'http://localhost/pub?id={token}',
                'sub_urls' => array(
                    'polling'      => 'http://localhost/sub-p/{tokens}',
                    'long-polling' => 'http://localhost/sub-lp/{tokens}',
                ),
                'filters'  => array(
                    'hash'   => array(
                        'class'  => 'hash',
                        'secret' => 'mysecret',
                        'algo'   => 'md5'
                    ),
                    'prefix' => array(
                        'class'  => 'prefix',
                        'prefix' => '123'
                    )
                )
            )
        );

        $config = $this->process($configs);

        $this->assertEquals(
            $config,
            array(
                'default_connection' => 'default',
                'connections'        => array(
                    'default' => array(
                        'pub_url'  => 'http://localhost/pub?id={token}',
                        'sub_urls' => array(
                            'polling'      => 'http://localhost/sub-p/{tokens}',
                            'long_polling' => 'http://localhost/sub-lp/{tokens}',
                        ),
                        'id_generator' => true,
                        'sender' => true,
                        'filters'  => array(
                            'hash'   => array(
                                'class'  => 'hash',
                                'secret' => 'mysecret',
                                'algo'   => 'md5'
                            ),
                            'prefix' => array(
                                'class'  => 'prefix',
                                'prefix' => '123'
                            )
                        ),
                    ),
                ),
            )
        );
    }

    protected function process($configs)
    {
        $processor = new Processor();

        return $processor->processConfiguration(new Configuration(), $configs);
    }
}
