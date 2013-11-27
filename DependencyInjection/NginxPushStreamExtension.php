<?php

namespace Alawar\NginxPushStreamBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NginxPushStreamExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        foreach ($config['connections'] as $name => $connection) {
            $this->buildConnection($container, $name, $connection);
        }
    }

    protected function buildConnection(ContainerBuilder $container, $name, $connection)
    {
        $definition = $container->setDefinition(
            sprintf('nginx_push_stream.%s_connection', $name),
            new DefinitionDecorator('nginx_push_stream.connection_prototype')
        );
        $definition->setArguments(array($connection['pub_url'], $connection['sub_urls']));
        foreach ($connection['filters'] as $filter) {
            $definition->addMethodCall('addFilter', $filter);
        }
    }
}
