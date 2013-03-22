<?php
/**
 * ClassMetadataTest.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Tests\Metadata;

use TP\SolariumExtensionsBundle\Metadata\ClassMetadata;

use TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub1;

/**
 * Class ClassMetadataTest
 *
 * @package TP\SolariumExtensionsBundle\Tests\Metadata
 */
class ClassMetadataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \TP\SolariumExtensionsBundle\Metadata\ClassMetadata
     */
    public $metadata;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->metadata = new ClassMetadata('TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub1');
    }

    public function testDefaultAttributes()
    {
        $this->assertClassHasAttribute('operations', 'TP\SolariumExtensionsBundle\Metadata\ClassMetadata');
        $this->assertClassHasAttribute('boost', 'TP\SolariumExtensionsBundle\Metadata\ClassMetadata');
        $this->assertClassHasAttribute('id', 'TP\SolariumExtensionsBundle\Metadata\ClassMetadata');
        $this->assertClassHasAttribute('mappingTable', 'TP\SolariumExtensionsBundle\Metadata\ClassMetadata');
    }

    public function testDefaultValues()
    {
        $this->assertEquals('TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub1', $this->metadata->name);
        $this->assertEquals(
            new \ReflectionClass('TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub1'),
            $this->metadata->reflection
        );
        $this->assertEquals(0.0, $this->metadata->boost);
        $this->assertEquals(array(), $this->metadata->operations);
        $this->assertEquals(array(), $this->metadata->mappingTable);
        $this->assertNull($this->metadata->id);
    }

    public function testSerializing()
    {
        $this->metadata->createdAt = new \DateTime('2012-03-24');
        $this->metadata->boost = 2.4;
        $this->metadata->mappingTable = array('test' => 'test');
        $this->metadata->id = 'test_id';

        $serialized = $this->metadata->serialize();

        $this->metadata = new ClassMetadata('TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub2');
        $this->metadata->unserialize($serialized);

        $this->assertEquals(new \DateTime('2012-03-24'), $this->metadata->createdAt);
        $this->assertEquals(2.4, $this->metadata->boost);
        $this->assertEquals(array('test' => 'test'), $this->metadata->mappingTable);
        $this->assertEquals('test_id', $this->metadata->id);
    }
}
