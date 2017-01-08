<?php

namespace Lexxpavlov\SpellingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('lexxpavlov_spelling');

        $rootNode
            ->children()
                ->scalarNode('entity_class')
                    ->defaultValue('AppBundle\\Entity\\Spelling')
                ->end()
                ->scalarNode('user_field')
                    ->defaultValue('username')
                ->end()
                ->scalarNode('find_by')
                    ->defaultValue('slug')
                ->end()
                ->scalarNode('data_delimiter')
                    ->defaultValue('#@')
                ->end()
                ->scalarNode('error_trans_domain')
                    ->defaultValue('LexxpavlovSpellingBundle')
                ->end()
                ->arrayNode('security')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('entity_class')
                            ->defaultValue('AppBundle\\Entity\\SpellingSecurity')
                        ->end()
                        ->scalarNode('allowed_role')
                            ->defaultNull()
                        ->end()
                        ->integerNode('query_interval')
                            ->defaultValue(5)
                            ->info('in seconds, default 5')
                        ->end()
                        ->integerNode('ban_period')
                            ->defaultValue(86400)
                            ->info('in seconds, default one day (86400 seconds)')
                        ->end()
                        ->integerNode('ban_check_period')
                            ->defaultValue(86400)
                            ->info('in seconds, default one day (86400 seconds)')
                        ->end()
                        ->integerNode('ban_count')
                            ->defaultValue(10)
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
