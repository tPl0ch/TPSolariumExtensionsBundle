<?php
/**
 * TPSolariumExtensionsExtensionTest.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Tests;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use TP\SolariumExtensionsBundle\DependencyInjection\Compiler\NelmioConfigSnifferPass;
use TP\SolariumExtensionsBundle\DependencyInjection\TPSolariumExtensionsExtension;
use Nelmio\SolariumBundle\DependencyInjection\NelmioSolariumExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Solarium\Client;

/**
 * Class TPSolariumExtensionsExtensionTest
 *
 * @package TP\SolariumExtensionsBundle\Tests
 */
class TPSolariumExtensionsExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testClientSniffing()
    {
        $config = array(
            'default_client' => 'client1',
            'clients' => array(
                'client1' => array(
                    'client_class' => 'TP\SolariumExtensionsBundle\Tests\StubClientOne'
                ),
                'client2' => array(
                    'client_class' => 'TP\SolariumExtensionsBundle\Tests\StubClientTwo'
                )
            ),
        );

        $container = $this->getTestContainer($config);

        $manager = $container->get('solarium_extensions.service_manager');

        $this->assertInstanceOf(
            'TP\SolariumExtensionsBundle\Manager\SolariumServiceManager',
            $manager
        );

        $this->assertInstanceOf(
            'TP\SolariumExtensionsBundle\Tests\StubClientOne',
            $manager->getClient('solarium.client.client1')
        );

        $this->assertInstanceOf(
            'TP\SolariumExtensionsBundle\Tests\StubClientTwo',
            $manager->getClient('solarium.client.client2')
        );

        $this->assertSame(
            $container->get('solarium.client.client1'),
            $manager->getClient('solarium.client.client1')
        );

        $this->assertSame(
            $container->get('solarium.client.client2'),
            $manager->getClient('solarium.client.client2')
        );
    }

    private function getTestContainer($config)
    {
        $container = $this->createContainer();
        $container->registerExtension(new NelmioSolariumExtension());
        $container->loadFromExtension('nelmio_solarium', $config);
        $container->registerExtension(new TPSolariumExtensionsExtension());
        $container->loadFromExtension('tp_solarium_extensions', array());

        $container->addCompilerPass(new NelmioConfigSnifferPass());

        $container->compile();

        return $container;
    }

    private function createContainer()
    {
        $container = new ContainerBuilder(new ParameterBag(array(
            'kernel.cache_dir' => __DIR__,
            'kernel.charset'   => 'UTF-8',
            'kernel.debug'     => false,
        )));

        return $container;
    }
}

class StubClientOne extends Client
{
}

class StubClientTwo extends Client
{
}
