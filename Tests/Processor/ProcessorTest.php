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

use TP\SolariumExtensionsBundle\Converter\ConverterCollection;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Field;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation;
use TP\SolariumExtensionsBundle\Metadata\ClassMetadata;
use TP\SolariumExtensionsBundle\Metadata\Driver\AnnotationDriver;
use TP\SolariumExtensionsBundle\Manager\SolariumServiceManager;
use TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub1;
use TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub2;
use TP\SolariumExtensionsBundle\Processor\Processor;
use TP\SolariumExtensionsBundle\Converter\ValueConverter;

use Metadata\MetadataFactory;

use Symfony\Component\PropertyAccess\PropertyAccess;
use TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub6;
use TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub7;
use TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub8;


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
        $this->processor = new Processor(
            new MetadataFactory(new AnnotationDriver(new AnnotationReader())),
            new SolariumServiceManager(),
            PropertyAccess::getPropertyAccessor(),
            $this->setUpConverter()
        );
    }

    public function testProcessingSave()
    {
        $object = new AnnotationStub1();

        $queryOne = $this->addClientGetQuery('solarium.client.save');

        $this->processor->process($object, Operation::OPERATION_SAVE);

        $commands = $queryOne->getCommands();

        $this->assertCount(1, $commands);
        $this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Add', $commands[0]);

        $documents = $commands[0]->getDocuments();

        $this->assertCount(1, $documents);
        $this->assertInstanceOf('Solarium\QueryType\Update\Query\Document\Document', $documents[0]);
        $this->assertEquals(2.4, $documents[0]->getBoost());

        $this->assertEquals(1423, $documents[0]->custom_id);
        $this->assertEquals('string', $documents[0]->string_s);
        $this->assertEquals('boosted_string', $documents[0]->boosted_field_s);
        $this->assertEquals(2.3, $documents[0]->getFieldBoost('boosted_field_s'));
        $this->assertEquals('inflectedNoMapping', $documents[0]->inflected_no_mapping);
        $this->assertEquals('mappedNoInflection', $documents[0]->mappedNoInflection_s);
        $this->assertEquals('noMappingNoInflection', $documents[0]->noMappingNoInflection);
        $this->assertEquals('customName', $documents[0]->my_custom_name_s);
        $this->assertEquals('objectWithPropertyAccess', $documents[0]->objectWithPropertyAccess);
        $this->assertEquals(24.35, $documents[0]->float_string_f);
        $this->assertEquals(22.55, $documents[0]->float_value_f);
        $this->assertEquals(25, $documents[0]->int_string_i);
        $this->assertEquals(26, $documents[0]->int_value_i);
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

        $this->processor
            ->getServiceManager()
            ->getClient('solarium.client.save')
            ->expects($this->once())
            ->method('update')
            ->with($finalQuery, 'test.endpoint')
        ;

        $this->processor->flush();
    }

    public function testProcessingDelete()
    {
        $object = new AnnotationStub8();

        $queryOne = $this->addClientGetQuery('solarium.client.delete');

        $this->processor->process($object, Operation::OPERATION_DELETE);

        $commands = $queryOne->getCommands();
        $this->assertCount(1, $commands);
        $this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Delete', $commands[0]);

        $this->assertEquals(array(1423), $commands[0]->getIds());

        $finalQuery = clone $queryOne;
        $finalQuery->addCommit();

        $this->processor
            ->getServiceManager()
            ->getClient('solarium.client.delete')
            ->expects($this->once())
            ->method('update')
            ->with($finalQuery)
        ;

        $this->processor->flush();
    }

    public function testProcessingUpdate()
    {
        $object = new AnnotationStub1();

        $queryOne = $this->addClientGetQuery('solarium.client.update');

        $this->processor->process($object, Operation::OPERATION_UPDATE);

        $commands = $queryOne->getCommands();

        $this->assertTrue($commands[0]->getOption('overwrite'));

        $finalQuery = clone $queryOne;
        $finalQuery->addCommit();

        $this->processor
            ->getServiceManager()
            ->getClient('solarium.client.update')
            ->expects($this->once())
            ->method('update')
            ->with($finalQuery, null)
        ;

        $this->processor->flush();
    }

    public function testNoProcessingWhenNotNeeded()
    {
        $object = new AnnotationStub6();
        $this->assertFalse($this->processor->process($object, Operation::OPERATION_SAVE));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Field 'collection' is declared as multi field, but property value is not traversable.
     */
    public function testExceptionNoTraversable()
    {
        $object = new AnnotationStub7();
        $this->addClientGetQuery('solarium.client.default');

        $this->processor->process($object, Operation::OPERATION_SAVE);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Property 'date' must be of type \DateTime, 'string' given.
     */
    public function testExceptionNoDateTime()
    {
        $object = new AnnotationStub7(true);
        $this->addClientGetQuery('solarium.client.default');

        $this->processor->process($object, Operation::OPERATION_SAVE);
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

    private function addClientGetQuery($id)
    {
        $mockClientOne = $this->getMockBuilder('Solarium\Client')->getMock();

        $queryOne = new Query();

        $mockClientOne->expects($this->once())
            ->method('createUpdate')
            ->will($this->returnValue($queryOne))
        ;

        $this->processor->getServiceManager()->setClient($mockClientOne, $id);

        return $queryOne;
    }

    private function setUpConverter()
    {
        $converter = new ConverterCollection();

        $classMap = array(
            Field::TYPE_DATE => 'TP\SolariumExtensionsBundle\Converter\Type\DateConverter',
            Field::TYPE_DATE_MULTI => 'TP\SolariumExtensionsBundle\Converter\Type\DateConverter',
            Field::TYPE_STRING => 'TP\SolariumExtensionsBundle\Converter\Type\StringConverter',
            Field::TYPE_STRING_MULTI => 'TP\SolariumExtensionsBundle\Converter\Type\StringConverter',
            Field::TYPE_TEXT => 'TP\SolariumExtensionsBundle\Converter\Type\StringConverter',
            Field::TYPE_TEXT_MULTI => 'TP\SolariumExtensionsBundle\Converter\Type\StringConverter',
            Field::TYPE_BOOLEAN => 'TP\SolariumExtensionsBundle\Converter\Type\BooleanConverter',
            Field::TYPE_BOOLEAN_MULTI => 'TP\SolariumExtensionsBundle\Converter\Type\BooleanConverter',
            Field::TYPE_FLOAT => 'TP\SolariumExtensionsBundle\Converter\Type\FloatConverter',
            Field::TYPE_FLOAT_MULTI => 'TP\SolariumExtensionsBundle\Converter\Type\FloatConverter',
            Field::TYPE_LONG => 'TP\SolariumExtensionsBundle\Converter\Type\FloatConverter',
            Field::TYPE_LONG_MULTI => 'TP\SolariumExtensionsBundle\Converter\Type\FloatConverter',
            Field::TYPE_DOUBLE => 'TP\SolariumExtensionsBundle\Converter\Type\FloatConverter',
            Field::TYPE_DOUBLE_MULTI => 'TP\SolariumExtensionsBundle\Converter\Type\FloatConverter',
            Field::TYPE_INT => 'TP\SolariumExtensionsBundle\Converter\Type\IntegerConverter',
            Field::TYPE_INT_MULTI => 'TP\SolariumExtensionsBundle\Converter\Type\IntegerConverter',
            Field::TYPE_LOCATION => 'TP\SolariumExtensionsBundle\Converter\ValueConverter',
        );

        foreach (Field::getFieldTypes() as $field) {
            $converter->registerConverter($field, $classMap[$field]);
        }

        return $converter;
    }
}
