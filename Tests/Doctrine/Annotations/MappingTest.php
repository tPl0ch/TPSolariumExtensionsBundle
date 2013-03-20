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

    /**
     * Tests the default mapping table
     */
    public function testDefaultMapping()
    {
        $this->assertEquals($this->getDefaultMappingArray(), Mapping::getDefaultMapping());
    }

    /**
     * Tests that the default behavior of the mapping paramter works
     */
    public function testMappingsParameter()
    {
        $this->mapping->mapping = array(
            Field::TYPE_INT     => '_test',
            Field::TYPE_BOOLEAN => '_bool',
        );

        $expected = $this->getDefaultMappingArray();
        $expected[Field::TYPE_INT]     = '_test';
        $expected[Field::TYPE_BOOLEAN] = '_bool';

        $this->assertEquals($expected, $this->mapping->getMappings());
        $this->assertEquals('_test', $this->mapping->getMapping(Field::TYPE_INT));
        $this->assertEquals('_bool', $this->mapping->getMapping(Field::TYPE_BOOLEAN));
    }

    /**
     * Tests that a call to get Mapping triggers mapping processing
     */
    public function testGetMappingTriggersProcessing()
    {
        $this->mapping->mapping = array(Field::TYPE_INT => '_test');
        $this->assertEquals('_test', $this->mapping->getMapping(Field::TYPE_INT));

        $expected = $this->getDefaultMappingArray();
        $expected[Field::TYPE_INT] = '_test';

        $this->assertEquals($expected, $this->mapping->getMappings());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid Field type 'INVALID' given. Only
     */
    public function testGetMappingInvalidType()
    {
        $this->mapping->getMapping('INVALID');
    }

    /**
     * Tests that you can restore the mappings to its defaults
     */
    public function testRestoreMapping()
    {
        $this->mapping->mapping = array(Field::TYPE_INT => '_test');
        $this->mapping->restoreMapping();

        $this->assertEquals('_i', $this->mapping->getMapping(Field::TYPE_INT));
        $this->assertEquals($this->getDefaultMappingArray(), $this->mapping->getMappings());
    }

    /**
     * Tests that invalid keys are silently ignored when strict checking is off
     */
    public function testInvalidKeysSilent()
    {
        $this->mapping->mapping = array(
            Field::TYPE_INT     => '_test',
            Field::TYPE_BOOLEAN => '_bool',
            'INVALID'           => 'INVALID'
        );

        $expected = $this->getDefaultMappingArray();
        $expected[Field::TYPE_INT]     = '_test';
        $expected[Field::TYPE_BOOLEAN] = '_bool';

        $this->assertEquals($expected, $this->mapping->getMappings());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid field type 'INVALID' found in 'mapping' and strict checking is enabled.
     */
    public function testInvalidKeysException()
    {
        $this->mapping->strict  = true;
        $this->mapping->mapping = array(
            Field::TYPE_INT     => '_test',
            Field::TYPE_BOOLEAN => '_bool',
            'INVALID'           => 'INVALID'
        );

        $this->mapping->getMappings();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid type for 'mapping' given. Expected 'Array', but got 'string'
     */
    public function testInvalidFieldType()
    {
        $this->mapping->mapping = 'blah';
        $this->mapping->getMappings();
    }

    /**
     * @return array
     */
    public function getDefaultMapping()
    {
        $return = array();
        foreach (Mapping::getDefaultMapping() as $type => $suffix) {
            $return[] = array($type, $suffix);
        }

        return $return;
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
