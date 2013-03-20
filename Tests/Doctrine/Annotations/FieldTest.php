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
}
