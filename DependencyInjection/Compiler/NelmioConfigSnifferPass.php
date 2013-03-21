<?php
/**
 * NelmioConfigSnifferPass.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\DependencyInjection\Compiler;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Nelmio\SolariumBundle\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class NelmioConfigSnifferPass
 *
 * @package TP\SolariumExtensionsBundle\DependencyInjection\Compiler
 */
class NelmioConfigSnifferPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $processor = new Processor();
        $configs = $container->getExtensionConfig('nelmio_solarium');
        $config = $processor->processConfiguration(new Configuration(), $configs);

        $manager = $container->getDefinition('solarium_extensions.service_manager');
        foreach ($config['clients'] as $name => $clientOptions) {
            $id = sprintf('solarium.client.%s', $name);
            $manager->addMethodCall('setClient', array(new Reference($id), $id));
        }

        $container->setDefinition('solarium_extensions.service_manager', $manager);
    }
}
