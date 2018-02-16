<?php

namespace Insidion\SwaggerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class InsidionSwaggerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        if (isset($config[ 'swagger' ])) {
            if ($config[ 'swagger' ][ 'host' ] === false) {
                unset($config[ 'swagger' ][ 'host' ]);
            }
            if ($config[ 'swagger' ][ 'basePath' ] === false) {
                unset($config[ 'swagger' ][ 'basePath' ]);
            }

            $container->setParameter("morlack.swagger.info", $config[ 'swagger' ]);
            $container->setParameter("morlack.swagger.cache", [
              'enabled' => $config[ 'cache' ],
              'service' => $config[ 'cache_service' ],
            ]);
        } else {
            $container->setParameter("morlack.swagger.info", []);
            $container->setParameter("morlack.swagger.cache", []);
        }

        $loader->load('services.yml');

        /**
         * Includes services_dev.yml only
         * if we are in debug mode
         */
        if(in_array($container->getParameter('kernel.environment'), ['dev', 'test'])){
            $loader->load('services_dev.yml');
        }
    }
}
