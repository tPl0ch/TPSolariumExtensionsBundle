<?php
/**
 * FieldTest.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Tests\Doctrine\Annotations;

use TP\SolariumExtensionsBundle\Doctrine\Annotations\Field;

/**
 * Class FieldTest
 *
 * @package TP\SolariumExtensionsBundle\Tests\Doctrine\Annotations
 * @group unit
 */
class FieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \TP\SolariumExtensionsBundle\Doctrine\Annotations\Field
     */
    public $field;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->field = new Field(array('name' => 'testVariableName'));
    }

    public function testDefaultType()
    {
        $this->assertEquals(Field::TYPE_STRING, $this->field->type);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid field type 'INVALID' given, only
     */
    public function testInvalidFieldTypeException()
    {
        $this->field = new Field(array('type' => 'INVALID'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Required 'propertyAccess' parameter for multi valued type 'boolean_multi' is missing.
     */
    public function testMissingPropertyAccessForMultiField()
    {
        $this->field = new Field(array('type' => Field::TYPE_BOOLEAN_MULTI));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Parameter 'boost' must be a numeric value.
     */
    public function testInvalidBoostValue()
    {
        $this->field = new Field(array('boost' => 'foo'));
    }

    public function testField()
    {
        $this->field = new Field(array('type' => Field::TYPE_DATE, 'boost' => '23', 'propertyAccess' => 'test_access'));

        $this->assertEquals(Field::TYPE_DATE, $this->field->type);
        $this->assertEquals(23.0, $this->field->boost);
        $this->assertNull($this->field->name);
        $this->assertEquals('test_access', $this->field->propertyAccess);
        $this->assertTrue($this->field->inflect);
        $this->assertTrue($this->field->useMapping);

        $this->field = new Field(array('type' => Field::TYPE_DATE_MULTI, 'propertyAccess' => 'test'));

        $this->assertEquals(Field::TYPE_DATE_MULTI, $this->field->type);
        $this->assertEquals('test', $this->field->propertyAccess);
        $this->assertEquals(0.0, $this->field->boost);
        $this->assertNull($this->field->name);

        $this->field = new Field(array('inflect' => false, 'useMapping' => false));
        $this->assertFalse($this->field->inflect);
        $this->assertFalse($this->field->useMapping);
    }

    /**
     * Tests that the field type name mapping works as expected
     */
    public function testNameInflectionSettings()
    {
        $mapping = array(Field::TYPE_STRING => '_s');
        $this->assertEquals('test_variable_name_s', $this->field->getFieldName($mapping));

        $mapping = array(Field::TYPE_STRING => '_ss');
        $this->field->name = 'test_variableName';
        $this->assertEquals('test_variable_name_ss', $this->field->getFieldName($mapping));

        $this->field->name = 'testVariableName';
        $this->field->inflect = false;
        $mapping = array(Field::TYPE_STRING => '_bb');
        $this->assertEquals('testVariableName_bb', $this->field->getFieldName($mapping));

        $this->field->useMapping = false;
        $this->assertEquals('testVariableName', $this->field->getFieldName($mapping));
    }

    public function testNameOverride()
    {
        $mapping = array(Field::TYPE_STRING => '_test');
        $this->assertEquals('test_variable_name_test', $this->field->getFieldName($mapping));

        $this->assertEquals('override_variable_name_test', $this->field->getFieldName($mapping, 'OverrideVariableName'));
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage No field name found.
     */
    public function testExceptionNoName()
    {
        $this->field->name = null;
        $this->field->getFieldName(array(Field::TYPE_STRING => '_test'));
    }
}
