<?php
/**
 * ProcessorTest.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Tests\Metadata;

use Doctrine\Common\Annotations\AnnotationReader;

use Solarium\Client;

use Solarium\QueryType\Update\Query\Query;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation;
use TP\SolariumExtensionsBundle\Metadata\ClassMetadata;
use TP\SolariumExtensionsBundle\Metadata\Driver\AnnotationDriver;
use TP\SolariumExtensionsBundle\Manager\SolariumServiceManager;
use TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub1;
use TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub2;
use TP\SolariumExtensionsBundle\Processor\Processor;

use Metadata\MetadataFactory;

use Symfony\Component\PropertyAccess\PropertyAccess;


/**
 * Class ProcessorTest
 *
 * @package TP\SolariumExtensionsBundle\Tests\Metadata
 */
class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Processor
     */
    public $processor;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $factory = new MetadataFactory(new AnnotationDriver(new AnnotationReader()));
        $manager = new SolariumServiceManager();

        $this->processor = new Processor($factory, $manager, PropertyAccess::getPropertyAccessor());
    }

    public function testProcessingSave()
    {
        $object = new AnnotationStub1();

        $mockClientOne = $this->getMockBuilder('Solarium\Client')->getMock();

        $queryOne = new Query();

        $mockClientOne->expects($this->once())
            ->method('createUpdate')
            ->will($this->returnValue($queryOne))
        ;

        $this->processor->getServiceManager()->setClient($mockClientOne, 'solarium.client.save');

        $this->processor->process($object, Operation::OPERATION_SAVE);

        $commands = $queryOne->getCommands();

        $this->assertCount(1, $commands);
        $this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Add', $commands[0]);

        $documents = $commands[0]->getDocuments();

        $this->assertCount(1, $documents);
        $this->assertInstanceOf('Solarium\QueryType\Update\Query\Document\Document', $documents[0]);

        $this->assertEquals(1423, $documents[0]->custom_id);
        $this->assertEquals('string', $documents[0]->string_s);
        $this->assertEquals('boosted_string', $documents[0]->boosted_field_s);
        $this->assertEquals(2.3, $documents[0]->getFieldBoost('boosted_field_s'));
        $this->assertEquals('inflectedNoMapping', $documents[0]->inflected_no_mapping);
        $this->assertEquals('mappedNoInflection', $documents[0]->mappedNoInflection_s);
        $this->assertEquals('noMappingNoInflection', $documents[0]->noMappingNoInflection);
        $this->assertEquals('customName', $documents[0]->my_custom_name_s);
        $this->assertFalse($documents[0]->bool_b);
        $this->assertEquals(array('test0', 'test1', 'test2'), $documents[0]->collection_tmulti);
        $this->assertEquals('2012-03-24T00:00:00Z', $documents[0]->date_dt);
        $expected = array(
            '2010-04-24T00:00:00Z',
            '2011-04-24T00:00:00Z',
            '2012-04-24T00:00:00Z'
        );
        $this->assertEquals($expected, $documents[0]->date_collection_dts);

        $finalQuery = clone $queryOne;
        $finalQuery->addCommit();

        $mockClientOne->expects($this->once())
            ->method('update')
            ->with($finalQuery)
        ;

        $this->processor->flush();
    }

    public function testNeedsProcessing()
    {
        $object = new AnnotationStub1();

        $this->assertTrue($this->processor->needsProcessing($object, Operation::OPERATION_UPDATE));
        $this->assertTrue($this->processor->needsProcessing($object, Operation::OPERATION_SAVE));
        $this->assertFalse($this->processor->needsProcessing($object, Operation::OPERATION_ALL));
        $this->assertFalse($this->processor->needsProcessing($object, Operation::OPERATION_DELETE));

        $object = new AnnotationStub2();

        $this->assertTrue($this->processor->needsProcessing($object, Operation::OPERATION_UPDATE));
        $this->assertTrue($this->processor->needsProcessing($object, Operation::OPERATION_SAVE));
        $this->assertTrue($this->processor->needsProcessing($object, Operation::OPERATION_ALL));
        $this->assertTrue($this->processor->needsProcessing($object, Operation::OPERATION_DELETE));
    }
}
