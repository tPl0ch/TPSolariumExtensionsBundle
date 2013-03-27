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

use TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation;
use TP\SolariumExtensionsBundle\Manager\SolariumServiceManager;
use TP\SolariumExtensionsBundle\Metadata\ClassMetadata;

use Solarium\Client;
use Solarium\QueryType\Update\Query\Query;

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
     * @var ClassMetadata
     */
    public $metadata;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->manager = new SolariumServiceManager();
        $this->metadata = new ClassMetadata('TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub1');
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

    public function testUpdateStackHandling()
    {
        $clientMockOne = $this->getMockBuilder('Solarium\Client')->getMock();
        $clientMockTwo = $this->getMockBuilder('Solarium\Client')->getMock();

        $this->manager->setClient($clientMockOne, 'solarium.client.save');
        $this->manager->setClient($clientMockTwo, 'solarium.client.update');

        $updateOne = new Query();
        $updateTwo = new Query();

        $clientMockOne
            ->expects($this->once())
            ->method('createUpdate')
            ->will($this->returnValue($updateOne))
        ;

        $clientMockTwo
            ->expects($this->once())
            ->method('createUpdate')
            ->will($this->returnValue($updateTwo))
        ;

        $clientMockOne
            ->expects($this->once())
            ->method('update')
            ->with($updateOne, 'endpoint.client.save')
        ;

        $clientMockTwo
            ->expects($this->once())
            ->method('update')
            ->with($updateTwo, null)
        ;

        $this->metadata->operations = array(
            Operation::OPERATION_SAVE   => 'solarium.client.save',
            Operation::OPERATION_UPDATE => 'solarium.client.update'
        );

        $this->metadata->endpoints = array(
            Operation::OPERATION_SAVE   => 'endpoint.client.save'
        );

        $this->assertSame($updateOne, $this->manager->getUpdateQuery($this->metadata, Operation::OPERATION_SAVE));
        $this->assertSame($updateTwo, $this->manager->getUpdateQuery($this->metadata, Operation::OPERATION_UPDATE));

        // This is to test that the objects are now fetched from the stack without calling the mocks
        $this->assertSame($updateOne, $this->manager->getUpdateQuery($this->metadata, Operation::OPERATION_SAVE));
        $this->assertSame($updateTwo, $this->manager->getUpdateQuery($this->metadata, Operation::OPERATION_UPDATE));

        $this->assertNull($this->manager->getUpdateQuery($this->metadata, Operation::OPERATION_DELETE));

        // Call update handler for all open update documents
        $this->manager->doUpdate();
    }
}
