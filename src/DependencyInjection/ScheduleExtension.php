<?php
declare(strict_types=1);

/*
 * This file is part of Kommunikatisten
 *
 * (c) kommunikatisten.net
 *
 * @license MIT
 */

namespace Kommunikatisten\ScheduleBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ScheduleExtension extends Extension
{
    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function load(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
#        $container->setParameter('kommunikatisten_schedule', $mergedConfig);
#        $this->processConfiguration(new Configuration(), $mergedConfig);

        $loader->load('services.yaml');
#        $loader->load('translation.yaml');
#        $loader->load('routes.yaml');
    }
}
