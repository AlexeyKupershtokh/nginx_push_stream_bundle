<?php

namespace Alawar\NginxPushStreamBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
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
        $serviceId = $this->getConnectionServiceId($name);
        $definition = $container->setDefinition(
            $serviceId,
            new Definition('%nginx_push_stream.connection.class%')
        );
        $definition->setArguments(array($connection['pub_url'], $connection['sub_urls']));

        // add id generator reference
        if ($connection['id_generator'] === true) {
            $definition->addMethodCall('setIdGenerator', array(new Reference('nginx_push_stream.id_generator')));
        } elseif (is_string($connection['id_generator'])) {
            $definition->addMethodCall('setIdGenerator', array(new Reference($connection['id_generator'])));
        }

        // add filter references
        foreach ($connection['filters'] as $id => $filter) {
            $filterServiceId = $this->buildFilter($container, $id, $filter);
            $definition->addMethodCall('addFilter', array(new Reference($filterServiceId)));
        }
    }

    protected function buildFilter(ContainerBuilder $container, $id, $filter)
    {
        $serviceId = $this->getFilterServiceId($id);
        $container->setDefinition(
            $serviceId,
            new Definition(sprintf('%%nginx_push_stream.filter.%s.class%%', $filter['class']), array($filter))
        );
        return $serviceId;
    }

    protected function getConnectionServiceId($id)
    {
        return sprintf('nginx_push_stream.%s_connection', $id);
    }

    protected function getFilterServiceId($id)
    {
        return sprintf('nginx_push_stream.%s_filter', $id);
    }
}
