<?php
/**
 * SolariumServiceManagerTest.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Tests\Manager;

use TP\SolariumExtensionsBundle\Manager\SolariumServiceManager;
use Solarium\Client;

/**
 * Class SolariumServiceManagerTest
 *
 * @package TP\SolariumExtensionsBundle\Tests\Manager
 */
class SolariumServiceManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SolariumServiceManager
     */
    public $manager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->manager = new SolariumServiceManager();
    }

    public function testClientSettingValid()
    {
        $client = new Client();
        $this->manager->setClient($client, 'id');

        $this->assertSame($client, $this->manager->getClient('id'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Solarium service with id 'INVALID' not found.
     */
    public function testClientSettingInvalid()
    {
        $this->manager->getClient('INVALID');
    }
}
