<?php


namespace Kommunikatisten\ContaoScheduleBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder('kommunikatisten_contao_schedule');
        $rootNode = method_exists($treeBuilder, 'getRootNode')
            ? $treeBuilder->getRootNode()
            : $treeBuilder->root('kommunikatisten_contao_schedule');

/*
        $rootNode
            ->children()
                ->arrayNode('twig')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('template')->defaultValue('@KommunikatistenContaoSchedule/menu.html.twig')->end()
                    ->end()
                ->end()
                ->booleanNode('templating')->defaultFalse()->end()
                ->scalarNode('default_renderer')->cannotBeEmpty()->defaultValue('twig')->end()
            ->end();
        return $treeBuilder;
*/
    }

}

