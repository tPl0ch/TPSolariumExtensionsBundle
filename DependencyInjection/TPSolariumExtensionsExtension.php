<?php
/**
 * TPSolariumExtensionsExtension.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Nelmio\SolariumBundle\DependencyInjection\Configuration as NelmioConfiguration;

/**
 * Class TPSolariumExtensionsExtension
 *
 * @package Nelmio\SolariumBundle\DependencyInjection
 */
class TPSolariumExtensionsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor     = new Processor();
        $configuration = new Configuration();
        $config        = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (!is_dir($config['metadata_cache_dir'])) {
            mkdir($config['metadata_cache_dir'], 0775, true);
        }

        if (!is_writable($config['metadata_cache_dir'])) {
            $message = "Metadata cache directory '%s' is not writable.";
            throw new InvalidConfigurationException(sprintf($message, $config['metadata_cache_dir']));
        }

        $container
            ->getDefinition('solarium_extensions.metadata.cache')
            ->replaceArgument(0, $config['metadata_cache_dir'])
        ;
    }
}
