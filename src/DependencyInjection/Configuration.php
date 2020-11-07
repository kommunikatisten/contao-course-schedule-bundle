<?php


namespace Kommunikatisten\ScheduleBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('kommunikatisten_schedule');
        $treeBuilder->getRootNode()
            ->children()
            ->booleanNode('isDisplay')->defaultTrue()->end()
            ->booleanNode('someBoolean')->defaultTrue()->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
