<?php
/**
 * AnnotationTest.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Tests\Doctrine\Annotations;

use TP\SolariumExtensionsBundle\Doctrine\Annotations\Annotation;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Field;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Document;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation;

/**
 * Class AnnotationTest
 *
 * @package TP\SolariumExtensionsBundle\Tests\Doctrine\Annotations
 */
class AnnotationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \TP\SolariumExtensionsBundle\Doctrine\Annotations\Annotation
     */
    public $annotation;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->annotation = new Annotation(array());
    }

    /**
     * @dataProvider getFieldTypeData
     */
    public function testIsValidTypeValid($type)
    {
        $this->assertTrue($this->annotation->isValidType($type));
    }

    /**
     * Tests invalid field returns false
     */
    public function testIsValidTypeInvalid()
    {
        $this->assertFalse($this->annotation->isValidType('INVALID'));
    }

    /**
     * @dataProvider getMultiFieldTypeData
     */
    public function testIsMultiTypeValid($type)
    {
        $this->assertTrue($this->annotation->isMultiValuedType($type));
    }

    /**
     * Tests invalid multi field returns false
     */
    public function testIsMultiTypeInvalid()
    {
        $this->assertFalse($this->annotation->isMultiValuedType(Field::TYPE_BOOLEAN));
    }

    /**
     * @dataProvider getOperationTypeData
     */
    public function testIsOperationValid($type)
    {
        $this->assertTrue($this->annotation->isValidOperation($type));
    }

    /**
     * Tests invalid operation returns false
     */
    public function testIsOperationInvalid()
    {
        $this->assertFalse($this->annotation->isValidOperation('INVALID'));
    }

    /**
     * @return array
     */
    public function getFieldTypeData()
    {
        $return = array();

        foreach (Field::getFieldTypes() as $type) {
            $return[] = array($type);
        }

        return $return;
    }

    /**
     * @return array
     */
    public function getMultiFieldTypeData()
    {
        $return = array();

        foreach (Field::getMultiFieldTypes() as $type) {
            $return[] = array($type);
        }

        return $return;
    }

    /**
     * @return array
     */
    public function getOperationTypeData()
    {
        $return = array();

        foreach (Operation::getOperationTypes() as $type) {
            $return[] = array($type);
        }

        return $return;
    }
}
