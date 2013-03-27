<?php
/**
 * ValueConverterTest.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Tests\Converter;

use Symfony\Component\PropertyAccess\PropertyAccess;
use TP\SolariumExtensionsBundle\Converter\ConverterCollection;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation;
use TP\SolariumExtensionsBundle\EventListener\DoctrineListener;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Field;
use TP\SolariumExtensionsBundle\Converter\ValueConverter;
use TP\SolariumExtensionsBundle\Metadata\PropertyMetadata;
use TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub1;

/**
 * Class ValueConverterTest
 *
 * @package TP\SolariumExtensionsBundle\Tests\Converter
 */
class ValueConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->accessor = PropertyAccess::getPropertyAccessor();
        $this->metadata = new PropertyMetadata('TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub1', 'string');
        $this->object = new AnnotationStub1();

        $this->converter = new ValueConverter();
    }

    public function testSingleValueExtraction()
    {
        $result = $this->converter->convert($this->object, $this->metadata, $this->accessor);

        $this->assertEquals('string', $result);

        $this->metadata = new PropertyMetadata(
            'TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub1',
            'objectWithPropertyAccess'
        );
        $this->metadata->propertyAccess = 'title';

        $result = $this->converter->convert($this->object, $this->metadata, $this->accessor);
        $this->assertEquals('objectWithPropertyAccess', $result);

        $this->metadata->multi = true;
        $this->metadata->propertyAccess = PropertyMetadata::TYPE_RAW;

        $this->assertSame(
            $this->object,
            $this->converter->convert($this->object, $this->metadata, $this->accessor)
        );
    }

    public function testMultiValueExtraction()
    {
        $this->metadata = new PropertyMetadata(
            'TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub1',
            'collection'
        );
        $this->metadata->multi = true;
        $this->metadata->propertyAccess = 'multiName';

        $expected = array('test0', 'test1', 'test2');
        $result = $this->converter->convertMulti($this->object, $this->metadata, $this->accessor);
        $this->assertEquals($expected, $result);

        $this->metadata = new PropertyMetadata(
            'TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub1',
            'dateCollection'
        );
        $this->metadata->multi = true;
        $this->metadata->propertyAccess = PropertyMetadata::TYPE_RAW;
        $expected = array(
            new \DateTime("2010-04-24", new \DateTimeZone('UTC')),
            new \DateTime("2011-04-24", new \DateTimeZone('UTC')),
            new \DateTime("2012-04-24", new \DateTimeZone('UTC'))
        );
        $result = $this->converter->convertMulti($this->object, $this->metadata, $this->accessor);
        $this->assertEquals($expected, $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Field 'collection' is declared as multi field, but property value is not traversable.
     */
    public function testExceptionWhenNotTraversable()
    {
        $this->metadata = new PropertyMetadata(
            'TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub1',
            'collection'
        );
        $this->metadata->multi = true;
        $this->object->collection = 'string';

        $this->converter->convertMulti($this->object, $this->metadata, $this->accessor);
    }
}
