<?php
/**
 * PropertyMetadataTest.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Tests\Metadata;

use TP\SolariumExtensionsBundle\Metadata\PropertyMetadata;

use TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub1;

/**
 * Class PropertyMetadataTest
 *
 * @package TP\SolariumExtensionsBundle\Tests\Metadata
 */
class PropertyMetadataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \TP\SolariumExtensionsBundle\Metadata\PropertyMetadata
     */
    public $metadata;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->metadata = new PropertyMetadata('TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub1', 'string');
    }

    public function testDefaultAttributes()
    {
        $this->assertClassHasAttribute('fieldName', 'TP\SolariumExtensionsBundle\Metadata\PropertyMetadata');
        $this->assertClassHasAttribute('type', 'TP\SolariumExtensionsBundle\Metadata\PropertyMetadata');
        $this->assertClassHasAttribute('boost', 'TP\SolariumExtensionsBundle\Metadata\PropertyMetadata');
        $this->assertClassHasAttribute('propertyAccess', 'TP\SolariumExtensionsBundle\Metadata\PropertyMetadata');
        $this->assertClassHasAttribute('multi', 'TP\SolariumExtensionsBundle\Metadata\PropertyMetadata');
    }

    public function testSerializing()
    {
        $this->metadata->fieldName = 'string_test';
        $this->metadata->multi = false;
        $this->metadata->propertyAccess = 'test_access';
        $this->metadata->boost = 2.3;
        $this->metadata->type = 'test_type';

        $serialized = $this->metadata->serialize();

        $this->metadata = new PropertyMetadata('TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub2', 'string2');
        $this->metadata->unserialize($serialized);

        $this->assertEquals('TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub1', $this->metadata->class);
        $this->assertEquals('string', $this->metadata->name);
        $this->assertEquals('string_test', $this->metadata->fieldName);
        $this->assertFalse($this->metadata->multi);
        $this->assertEquals(2.3, $this->metadata->boost);
        $this->assertEquals('test_type', $this->metadata->type);
        $this->assertEquals('test_access', $this->metadata->propertyAccess);
    }
}
