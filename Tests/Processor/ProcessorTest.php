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

use TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation;
use TP\SolariumExtensionsBundle\Metadata\ClassMetadata;
use TP\SolariumExtensionsBundle\Metadata\Driver\AnnotationDriver;
use TP\SolariumExtensionsBundle\Manager\SolariumServiceManager;
use TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub1;
use TP\SolariumExtensionsBundle\Processor\Processor;

use Metadata\MetadataFactory;

use Symfony\Component\PropertyAccess\PropertyAccess;
use TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub2;

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
