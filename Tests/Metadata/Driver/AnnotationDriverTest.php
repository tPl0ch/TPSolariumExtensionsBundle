<?php
/**
 * AnnotationDriverTest.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;

use TP\SolariumExtensionsBundle\Doctrine\Annotations\Mapping;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Field;
use TP\SolariumExtensionsBundle\Metadata\Driver\AnnotationDriver;

use TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub1;
use TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub2;
use TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub4;
use TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub5;
use TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub6;


/**
 * Class AnnotationDriverTest
 *
 * @package TP\SolariumExtensionsBundle\Tests\Metadata\Driver
 */
class AnnotationDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \TP\SolariumExtensionsBundle\Metadata\Driver\AnnotationDriver
     */
    public $driver;

    /**
     * @var \TP\SolariumExtensionsBundle\Metadata\ClassMetadata
     */
    public $metadata;
    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->driver = new AnnotationDriver(new AnnotationReader());
        $this->metadata = $this->driver->loadMetadataForClass(new \ReflectionClass(new AnnotationStub1()));
    }

    public function testClassAnnotationClasses()
    {
        $expected = array(
            AnnotationDriver::ANNOTATION_DOCUMENT,
            AnnotationDriver::ANNOTATION_MAPPING
        );
        $this->assertEquals($expected, AnnotationDriver::getClassAnnotationClasses());
    }

    public function testPropertyAnnotationClasses()
    {
        $expected = array(
            AnnotationDriver::ANNOTATION_FIELD,
            AnnotationDriver::ANNOTATION_ID
        );
        $this->assertEquals($expected, AnnotationDriver::getPropertyAnnotationClasses());
    }

    public function testClassAnnotationParsing()
    {
        $this->assertEquals('custom_id', $this->metadata->id);
        $this->assertEquals('id', $this->metadata->idPropertyAccess);
        $this->assertEquals(2.4, $this->metadata->boost);
        $expected = array(
            Operation::OPERATION_SAVE => 'solarium.client.save',
            Operation::OPERATION_UPDATE => 'solarium.client.update'
        );
        $this->assertEquals($expected, $this->metadata->operations);

        $expected = Mapping::getDefaultMapping();
        $expected[Field::TYPE_TEXT_MULTI] = '_tmulti';
        $this->assertEquals($expected, $this->metadata->mappingTable);
    }

    public function testPropertyAnnotationParsing()
    {
        /** @var \TP\SolariumExtensionsBundle\Metadata\PropertyMetadata $prop  */
        $prop = $this->metadata->propertyMetadata['string'];

        $this->assertEquals('string_s', $prop->fieldName);
        $this->assertEquals(Field::TYPE_STRING, $prop->type);
        $this->assertEquals(0.0, $prop->boost);
        $this->assertNull($prop->propertyAccess);
        $this->assertFalse($prop->multi);

        $prop = $this->metadata->propertyMetadata['boostedField'];
        $this->assertEquals(2.3, $prop->boost);
        $this->assertEquals('boosted_field_s', $prop->fieldName);

        $prop = $this->metadata->propertyMetadata['inflectedNoMapping'];
        $this->assertEquals('inflected_no_mapping', $prop->fieldName);

        $prop = $this->metadata->propertyMetadata['mappedNoInflection'];
        $this->assertEquals('mappedNoInflection_s', $prop->fieldName);

        $prop = $this->metadata->propertyMetadata['noMappingNoInflection'];
        $this->assertEquals('noMappingNoInflection', $prop->fieldName);

        $prop = $this->metadata->propertyMetadata['customName'];
        $this->assertEquals('my_custom_name_s', $prop->fieldName);

        $prop = $this->metadata->propertyMetadata['bool'];
        $this->assertEquals(Field::TYPE_BOOLEAN, $prop->type);
        $this->assertEquals('bool_b', $prop->fieldName);

        $prop = $this->metadata->propertyMetadata['collection'];
        $this->assertTrue($prop->multi);
        $this->assertEquals('multiName', $prop->propertyAccess);
    }

    public function testDefaultMappingSetWhenNoMappingFound()
    {
        $this->metadata = $this->driver->loadMetadataForClass(new \ReflectionClass(new AnnotationStub2()));
        $this->assertEquals(Mapping::getDefaultMapping(), $this->metadata->mappingTable);
    }

    public function testNoDocumentAnnotation()
    {
        $this->metadata = $this->driver->loadMetadataForClass(new \ReflectionClass(new AnnotationStub6()));
        $this->assertNull($this->metadata);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage The class 'TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub5' has a Solarium Document Declaration, but no Id field.
     */
    public function testNoIdLogicException()
    {
        $this->metadata = $this->driver->loadMetadataForClass(new \ReflectionClass(new AnnotationStub5()));
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Duplicate Id field declaration for class 'TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub4' found.
     */
    public function testDuplicateIdLogicException()
    {
        $this->metadata = $this->driver->loadMetadataForClass(new \ReflectionClass(new AnnotationStub4()));
    }
}
