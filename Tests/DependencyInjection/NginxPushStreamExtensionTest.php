<?php

namespace Alawar\NginxPushStreamBundle\Tests\Connection\DependencyInjection;

use Alawar\NginxPushStreamBundle\DependencyInjection\NginxPushStreamExtension;
use Alawar\NginxPushStreamBundle\Tests\DependencyInjection\DependencyInjectionTest;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

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

        $this->assertTrue($container->hasDefinition('nginx_push_stream.default_connection'));
        $defaultConnection = $container->getDefinition('nginx_push_stream.default_connection');

        $this->assertDICDefinitionMethodCallAt(0, $defaultConnection, 'setIdGenerator', array(
            new Reference('nginx_push_stream.id_generator')
        ));

        $this->assertDICDefinitionMethodCallAt(1, $defaultConnection, 'setSender', array(
            new Reference('nginx_push_stream.sender')
        ));

        $connection = $container->get('nginx_push_stream.default_connection');
        $this->assertInstanceOf('\Alawar\NginxPushStreamBundle\Connection', $connection);
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

    public function testIdGeneratorAndSenderDisabled()
    {
        $configs = array(
            array(
                'pub_url'  => 'http://localhost/pub?id={token}',
                'sub_urls' => array(
                    'polling'      => 'http://localhost/sub-p/{tokens}',
                ),
                'id_generator' => false,
                'sender' => false,
            )
        );

        $container = $this->getContainer($configs);

        $this->assertTrue($container->hasDefinition('nginx_push_stream.default_connection'));
        $defaultConnection = $container->getDefinition('nginx_push_stream.default_connection');
        $this->assertCount(0, $defaultConnection->getMethodCalls());
    }

    public function testIdGeneratorCustomService()
    {
        $configs = array(
            array(
                'pub_url'  => 'http://localhost/pub?id={token}',
                'sub_urls' => array(
                    'polling'      => 'http://localhost/sub-p/{tokens}',
                ),
                'id_generator' => 'nginx_push_stream.my_id_generator',
            )
        );

        $container = $this->getContainer($configs);

        $this->assertTrue($container->hasDefinition('nginx_push_stream.default_connection'));
        $defaultConnection = $container->getDefinition('nginx_push_stream.default_connection');
        $this->assertDICDefinitionMethodCallAt(0, $defaultConnection, 'setIdGenerator', array(
                new Reference('nginx_push_stream.my_id_generator')
        ));
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
                    'hash' => array('class' => 'hash', 'secret' => 'x'),
                    'prefix' => array('class' => 'prefix', 'prefix' => 'x'),
                )
            )
        );

        $container = $this->getContainer($configs);

        $this->assertTrue($container->hasDefinition('nginx_push_stream.default_connection'));
        $defaultConnection = $container->getDefinition('nginx_push_stream.default_connection');
        $this->assertDICDefinitionMethodCallAt(0, $defaultConnection, 'setIdGenerator');
        $this->assertDICDefinitionMethodCallAt(1, $defaultConnection, 'setSender');
        $this->assertDICDefinitionMethodCallAt(2, $defaultConnection, 'addFilter', array(
                new Reference('nginx_push_stream.hash_filter')
        ));
        $this->assertDICDefinitionMethodCallAt(3, $defaultConnection, 'addFilter', array(
                new Reference('nginx_push_stream.prefix_filter')
        ));

        $this->assertTrue($container->hasDefinition('nginx_push_stream.hash_filter'));
        $hashFilterDefinition = $container->getDefinition('nginx_push_stream.hash_filter');
        $this->assertDICDefinitionClass($hashFilterDefinition, '%nginx_push_stream.filter.hash.class%');

        $this->assertTrue($container->hasDefinition('nginx_push_stream.prefix_filter'));
        $prefixFilterDefinition = $container->getDefinition('nginx_push_stream.prefix_filter');
        $this->assertDICDefinitionClass($prefixFilterDefinition, '%nginx_push_stream.filter.prefix.class%');
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
