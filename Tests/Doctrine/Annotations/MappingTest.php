<?php
/**
 * MappingTest.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Tests\Doctrine\Annotations;

use TP\SolariumExtensionsBundle\Doctrine\Annotations\Mapping;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Field;

/**
 * Class MappingTest
 *
 * @package TP\SolariumExtensionsBundle\Tests\Doctrine\Annotations
 */
class MappingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \TP\SolariumExtensionsBundle\Doctrine\Annotations\Mapping
     */
    public $mapping;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->mapping = new Mapping(array());
    }

    public function testDefaultMappingsGetSet()
    {
        $this->assertEquals($this->getDefaultMappingArray(), $this->mapping->getMappings());
    }

    public function testDefaultMapping()
    {
        $this->assertEquals($this->getDefaultMappingArray(), Mapping::getDefaultMapping());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid type for 'mapping' given. Expected 'Array', but got 'string'
     */
    public function testInvalidMappingValue()
    {
        $this->mapping = new Mapping(array('mapping' => 'foo'));
    }

    public function testMappingGetsMergedWithDefault()
    {
        $this->mapping = new Mapping(array(
            'mapping' => array(
                Field::TYPE_BOOLEAN => '_test'
            )
        ));

        $expected = $this->getDefaultMappingArray();
        $expected[Field::TYPE_BOOLEAN] = '_test';

        $this->assertEquals($expected, $this->mapping->getMappings());
    }

    public function testInvalidKeysGetDroppedWhenStrictFalse()
    {
        $this->mapping = new Mapping(array(
            'mapping' => array(
                Field::TYPE_BOOLEAN => '_test',
                'INVALID' => 'INVALID'
            )
        ));

        $expected = $this->getDefaultMappingArray();
        $expected[Field::TYPE_BOOLEAN] = '_test';

        $this->assertEquals($expected, $this->mapping->getMappings());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid field type 'INVALID' found in 'mapping' and strict checking is enabled.
     */
    public function testInvalidKeysRaiseExceptionStrictTrue()
    {
        $this->mapping = new Mapping(array(
            'strict'  => true,
            'mapping' => array(
                Field::TYPE_BOOLEAN => '_test',
                'INVALID' => 'INVALID'
            )
        ));
    }

    public function testGetMapping()
    {
        $this->mapping = new Mapping(array(
            'mapping' => array(
                Field::TYPE_BOOLEAN => '_test',
                Field::TYPE_DATE    => '_date'
            )
        ));

        $this->assertEquals('_test', $this->mapping->getMapping(Field::TYPE_BOOLEAN));
        $this->assertEquals('_date', $this->mapping->getMapping(Field::TYPE_DATE));

        $this->assertNull($this->mapping->getMapping('INVALID'));
    }

    public function testRestoreMapping()
    {
        $this->mapping = new Mapping(array(
            'mapping' => array(
                Field::TYPE_BOOLEAN => '_test',
                Field::TYPE_DATE    => '_date'
            )
        ));

        $this->mapping->restoreMapping();

        $this->assertEquals($this->getDefaultMappingArray(), $this->mapping->getMappings());
    }

    /**
     * @return array
     */
    private function getDefaultMappingArray()
    {
        return array(
            Field::TYPE_INT           => '_i',
            Field::TYPE_INT_MULTI     => '_is',
            Field::TYPE_STRING        => '_s',
            Field::TYPE_STRING_MULTI  => '_ss',
            Field::TYPE_LONG          => '_l',
            Field::TYPE_LONG_MULTI    => '_ls',
            Field::TYPE_TEXT          => '_t',
            Field::TYPE_TEXT_MULTI    => '_txt',
            Field::TYPE_BOOLEAN       => '_b',
            Field::TYPE_BOOLEAN_MULTI => '_bs',
            Field::TYPE_FLOAT         => '_f',
            Field::TYPE_FLOAT_MULTI   => '_fs',
            Field::TYPE_DOUBLE        => '_d',
            Field::TYPE_DOUBLE_MULTI  => '_ds',
            Field::TYPE_DATE          => '_dt',
            Field::TYPE_DATE_MULTI    => '_dts',
            Field::TYPE_LOCATION      => '_p',
        );
    }
}
