<?php

namespace Alawar\NginxPushStreamBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('nginx_push_stream');

        $rootNode
            ->beforeNormalization()
                ->ifTrue(function ($v) {
                    return is_array($v) && !array_key_exists('connections', $v) && !array_key_exists('connection', $v);
                })
                ->then(function ($v) {
                    // Key that should not be rewritten to the connection config
                    $excludedKeys = array('default_connection' => true);
                    $connection = array_diff_key($v, $excludedKeys);
                    $v = array_intersect_key($v, $excludedKeys);
                    $v['default_connection'] = isset($v['default_connection']) ? (string)$v['default_connection'] : 'default';
                    $v['connections'] = array($v['default_connection'] => $connection);

                    return $v;
                })
            ->end()
            ->children()
                ->scalarNode('default_connection')->end()
            ->end()
            ->append($this->getConnectionsNode())
        ->end();

        return $treeBuilder;
    }

    /**
     * Builds a node to handle connection configs
     *
     * @return ArrayNodeDefinition
     */
    public function getConnectionsNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('connections');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->fixXmlConfig('sub_url')
            ->fixXmlConfig('filter')
            ->performNoDeepMerging()
            ->prototype('array')
            ->children()
                ->scalarNode('pub_url')
                    ->isRequired()
                    ->example('http://example.com/pub/?id={token}')
                ->end()
                ->arrayNode('sub_urls')
                    ->isRequired()
                    ->prototype('scalar')
                    ->end()
                    ->example(array(
                        'polling' => 'http://example.com/sub-p/{tokens}',
                        'long-polling' => 'http://example.com/sub-lp/{tokens}',
                        'streaming' => 'http://example.com/sub-s/{tokens}',
                        'eventsource' => 'http://example.com/sub-ev/{tokens}',
                    ))
                ->end()
                ->scalarNode('id_generator')
                    ->example(true)
                ->end()
                ->arrayNode('filters')
                    ->performNoDeepMerging()
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->prototype('scalar')
                        ->end()
                        ->children()
                            ->scalarNode('class')->end()
                        ->end()
                    ->end()
                    ->example(array(
                        'hash' => array('class' => 'hash', 'secret' => 'mysecret', 'algo' => 'md5'),
                        'prefix' => array('class' => 'prefix', 'prefix' => 'myapp_'),
                    ))
                ->end()
            ->end()
        ;

        return $node;
    }
}
