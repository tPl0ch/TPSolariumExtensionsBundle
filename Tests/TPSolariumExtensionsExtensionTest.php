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

use Solarium\Core\Client\Endpoint;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

use TP\SolariumExtensionsBundle\DependencyInjection\Compiler\NelmioConfigSnifferPass;
use TP\SolariumExtensionsBundle\DependencyInjection\TPSolariumExtensionsExtension;

use Nelmio\SolariumBundle\DependencyInjection\NelmioSolariumExtension;

use Solarium\Client;


/**
 * Class TPSolariumExtensionsExtensionTest
 *
 * @package TP\SolariumExtensionsBundle\Tests
 */
class TPSolariumExtensionsExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public $container;

    /**
     * @var array
     */
    public $configNelmio = array(
        'endpoints' => array(
            'default' => array(
                'host' => 'localhost',
                'port' => '8080',
                'path' => 'solr-jobzauberer',
                'core' => 'jobzauberer_test',
                'timeout' => 5
            ),
            'test' => array(
                'host' => 'localhost',
                'port' => '8888',
                'path' => 'test',
                'core' => 'test_core',
                'timeout' => 15
            )
        ),
        'default_client' => 'client1',
        'clients' => array(
            'client1' => array(
                'client_class' => 'TP\SolariumExtensionsBundle\Tests\StubClientOne',
                'endpoints' => array('default')
            ),
            'client2' => array(
                'client_class' => 'TP\SolariumExtensionsBundle\Tests\StubClientTwo',
                'endpoints'    => array('test', 'default')
            )
        ),
    );

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $configTp = array(
            'metadata_cache_dir' => '%kernel.cache_dir%/metadata_test_cache'
        );

        $this->container = $this->getTestContainer($this->configNelmio, $configTp);
    }

    public function testCacheDirOption()
    {
        $this->assertFileExists(__DIR__ . '/metadata_test_cache');

        $configTp = array(
            'metadata_cache_dir' => '%kernel.cache_dir%/metadata_test_cache_two'
        );

        $this->container = $this->getTestContainer($this->configNelmio, $configTp);

        $this->assertFileExists(__DIR__ . '/metadata_test_cache_two');
        rmdir(__DIR__ . '/metadata_test_cache_two');
    }

    public function testDoctrineListenerInitialized()
    {
        $listener = $this->container->get('solarium_extensions.doctrine_listener');

        $this->assertInstanceOf(
            $this->container->getParameter('solarium_extensions.doctrine_listener.class'),
            $listener
        );
    }

    public function testProcessorCreation()
    {
        $processor = $this->container
            ->get('solarium_extensions.doctrine_listener')
            ->getProcessor()
        ;

        $this->assertInstanceOf(
            $this->container->getParameter('solarium_extensions.processor.class'),
            $processor
        );

        $this->assertInstanceOf(
            $this->container->getParameter('solarium_extensions.metadata_factory.class'),
            $processor->getMetadataFactory()
        );

        $this->assertInstanceOf(
            $this->container->getParameter('solarium_extensions.service_manager.class'),
            $processor->getServiceManager()
        );
    }

    public function testClientSniffing()
    {
        $manager = $this->container
            ->get('solarium_extensions.doctrine_listener')
            ->getProcessor()
            ->getServiceManager()
        ;

        $this->assertInstanceOf(
            'TP\SolariumExtensionsBundle\Manager\SolariumServiceManager',
            $manager
        );

        $this->assertInstanceOf(
            'TP\SolariumExtensionsBundle\Tests\StubClientOne',
            $manager->getClient('solarium.client.client1')
        );

        $endpointDefault = new Endpoint(array(
            'host' => 'localhost',
            'port' => '8080',
            'path' => 'solr-jobzauberer',
            'core' => 'jobzauberer_test',
            'timeout' => 5,
            'key' => 'default'
        ));

        $expected = array(
            'default' => $endpointDefault
        );

        $this->assertEquals(
            $expected,
            $manager->getClient('solarium.client.client1')->getEndpoints()
        );

        $this->assertInstanceOf(
            'TP\SolariumExtensionsBundle\Tests\StubClientTwo',
            $manager->getClient('solarium.client.client2')
        );

        $endpointTest = new Endpoint(array(
            'host' => 'localhost',
            'port' => '8888',
            'path' => 'test',
            'core' => 'test_core',
            'timeout' => 15,
            'key' => 'test'
        ));

        $expected = array(
            'test'    => $endpointTest,
            'default' => $endpointDefault
        );

        $this->assertEquals(
            $expected,
            $manager->getClient('solarium.client.client2')->getEndpoints()
        );

        $this->assertSame(
            $this->container->get('solarium.client.client1'),
            $manager->getClient('solarium.client.client1')
        );

        $this->assertSame(
            $this->container->get('solarium.client.client2'),
            $manager->getClient('solarium.client.client2')
        );
    }

    private function getTestContainer($configNelmio, $configTp)
    {
        $container = $this->createContainer();
        $container->registerExtension(new NelmioSolariumExtension());
        $container->loadFromExtension('nelmio_solarium', $configNelmio);
        $container->registerExtension(new TPSolariumExtensionsExtension());
        $container->loadFromExtension('tp_solarium_extensions', $configTp);

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

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        rmdir(__DIR__ . '/metadata_test_cache');
    }
}

class StubClientOne extends Client
{
}

class StubClientTwo extends Client
{
}
