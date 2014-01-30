<?php

namespace Alawar\NginxPushStreamBundle\Tests\Connection\DependencyInjection;

use Alawar\NginxPushStreamBundle\DependencyInjection\NginxPushStreamExtension;
use Alawar\NginxPushStreamBundle\Tests\DependencyInjection\DependencyInjectionTest;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class NginxPushStreamExtensionTest extends DependencyInjectionTest
{
    public function testDefaultConfig()
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

        $container = $this->getContainer($configs);

        $this->assertTrue($container->hasDefinition('nginx_push_stream.connection_prototype'));

        $connectionPrototype = $container->getDefinition('nginx_push_stream.connection_prototype');
        $this->assertDICDefinitionClass($connectionPrototype, '%nginx_push_stream.connection.class%');

        $this->assertTrue($container->hasDefinition('nginx_push_stream.default_connection'));
        $defaultConnection = $container->getDefinition('nginx_push_stream.default_connection');
        $this->assertEquals('nginx_push_stream.connection_prototype', $defaultConnection->getParent());
    }

    public function testManyConnections()
    {
        $configs = array(
            array(
                'connections' => array(
                    'foo' => array(
                        'pub_url'  => 'http://localhost/pub?id={token}',
                        'sub_urls' => array(
                            'polling'      => 'http://localhost/sub-p/{tokens}',
                        )
                    ),
                    'bar' => array(
                        'pub_url'  => 'http://bar/pub?id={token}',
                        'sub_urls' => array(
                            'polling'      => 'http://localhost/sub-p/{tokens}',
                        )
                    )
                )
            )
        );

        $container = $this->getContainer($configs);

        $this->assertTrue($container->hasDefinition('nginx_push_stream.foo_connection'));
        $this->assertTrue($container->hasDefinition('nginx_push_stream.bar_connection'));
    }

    public function testFilters()
    {
        $configs = array(
            array(
                'pub_url'  => 'http://localhost/pub?id={token}',
                'sub_urls' => array(
                    'polling'      => 'http://localhost/sub-p/{tokens}',
                ),
                'filters' => array(
                    'hash' => array('secret' => 'x'),
                    'prefix' => array('prefix' => 'x'),
                )
            )
        );

        $container = $this->getContainer($configs);

        $this->assertTrue($container->hasDefinition('nginx_push_stream.default_connection'));
        $defaultConnection = $container->getDefinition('nginx_push_stream.default_connection');
        $this->assertDICDefinitionMethodCallAt(0, $defaultConnection, 'addFilter');
    }

    protected function getContainer(array $config = array())
    {
        $container = new ContainerBuilder();
        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        //$container->addCompilerPass(new LoggerChannelPass());

        $loader = new NginxPushStreamExtension();
        $loader->load($config, $container);
        $container->compile();

        return $container;
    }
}
