<?php
/**
 * ConverterCollectionTest.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Tests\Converter;

use TP\SolariumExtensionsBundle\Converter\ConverterCollection;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation;
use TP\SolariumExtensionsBundle\EventListener\DoctrineListener;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Field;
use TP\SolariumExtensionsBundle\Converter\ValueConverter;

/**
 * Class ConverterCollectionTest
 *
 * @package TP\SolariumExtensionsBundle\Tests\Converter
 */
class ConverterCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConverterCollection
     */
    public $converter;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->converter = new ConverterCollection();
    }

    public function testConverterRegistration()
    {
        $converter = $this->converter->registerConverter(
            Field::TYPE_DATE,
            'TP\SolariumExtensionsBundle\Converter\ValueConverter'
        );

        $this->assertSame($this->converter, $converter);

        $this->converter->registerConverter(
            Field::TYPE_DATE_MULTI,
            'TP\SolariumExtensionsBundle\Converter\ValueConverter'
        );

        $this->assertInstanceOf(
            'TP\SolariumExtensionsBundle\Converter\ValueConverter',
            $this->converter->getConverter(Field::TYPE_DATE)
        );

        $this->assertInstanceOf(
            'TP\SolariumExtensionsBundle\Converter\ValueConverter',
            $this->converter->getConverter(Field::TYPE_DATE_MULTI)
        );

        $this->assertSame(
            $this->converter->getConverter(Field::TYPE_DATE),
            $this->converter->getConverter(Field::TYPE_DATE_MULTI)
        );

        $this->assertNull($this->converter->getConverter(Field::TYPE_STRING));
    }

    public function testHasConverter()
    {
        $this->converter->registerConverter(
            Field::TYPE_DATE,
            'TP\SolariumExtensionsBundle\Converter\ValueConverter'
        );

        $this->assertTrue($this->converter->hasConverter(Field::TYPE_DATE));
        $this->assertFalse($this->converter->hasConverter(Field::TYPE_DATE_MULTI));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Class '\stdClass' must implement 'TP\SolariumExtensionsBundle\Converter\ValueConverterInterface'
     */
    public function testExceptionOnInvalidConverterClass()
    {
        $this->converter->registerConverter(Field::TYPE_DATE, '\stdClass');
    }
}
