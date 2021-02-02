<?php

/*
 * This file is part of Kommunikatisten\ContaoScheduleBundle
 *
 * (c) kommunikatisten.net
 *
 * @license MIT
 */

namespace Kommunikatisten\ContaoScheduleBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ContaoScheduleExtension extends Extension
{
    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.yaml');
/*
        if (isset($config['twig'])) {
            $loader->load('twig.yaml');
            $container->loadFromExtension('twig', [
                'paths' => [
                    '%kernel.project_dir%/vendor/kommunikatisten/contao-schedule-bundle/Resources/views' => 'KommunikatistenContaoSchedule',
                ]
            ]);
        }
*/
    }
}
