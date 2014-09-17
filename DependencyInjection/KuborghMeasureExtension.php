<?php

namespace Kuborgh\Bundle\MeasureBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KuborghMeasureExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // add the measurement classes to the mananger

        // List
        if(count($config['content_type_list_measurer'])) {
            $measureManager = $container->findDefinition('kuborgh_measure.listservice.contenttypeload');
            foreach($config['content_type_list_measurer'] as $processor) {
                $serviceId = $processor['service'];
                $measureManager->addMethodCall('addMeasurer', array(new Reference($serviceId)));
            }
        }

        // Single
        if(count($config['content_type_single_measurer'])) {
            $measureManager = $container->findDefinition('kuborgh_measure.singleservice.contenttypeload');
            foreach($config['content_type_single_measurer'] as $processor) {
                $serviceId = $processor['service'];
                $measureManager->addMethodCall('addMeasurer', array(new Reference($serviceId)));
            }
        }
    }
}
