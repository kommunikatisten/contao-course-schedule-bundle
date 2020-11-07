<?php


namespace Kommunikatisten\ContaoScheduleBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Exception;
use Kommunikatisten\ContaoScheduleBundle\ContaoScheduleBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(ContaoScheduleBundle::class)
                ->setLoadAfter([
                    TwigBundle::class,
                    ContaoCoreBundle::class
                ]),
        ];
    }

    /**
     * @param LoaderResolverInterface $resolver
     * @param KernelInterface $kernel
     * @return mixed
     * @throws Exception
     */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel) {
        $file = __DIR__.'/../Resources/config/routes.yaml';
        return $resolver->resolve($file)->load($file);
    }
}
