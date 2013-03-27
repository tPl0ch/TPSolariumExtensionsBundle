<?php
/**
 * OperationTest.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Tests\Doctrine\Annotations;

use TP\SolariumExtensionsBundle\Doctrine\Annotations\Document;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Field;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation;

/**
 * Class OperationTest
 *
 * @package TP\SolariumExtensionsBundle\Tests\Doctrine\Annotations
 */
class OperationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation
     */
    public $operation;

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Required operation type for 'Operation' Annotation is missing.
     */
    public function testMissingValueException()
    {
        $this->operation = new Operation(array());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid operation value 'INVALID'. Only save,update,delete,all are supported.
     */
    public function testInvalidTypeException()
    {
        $this->operation = new Operation(array('value' => 'INVALID'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Required 'service' parameter for operation 'delete' is missing.
     */
    public function testMissingServiceParameter()
    {
        $this->operation = new Operation(array('value' => Operation::OPERATION_DELETE));
    }

    public function testValidOperation()
    {
        $this->operation = new Operation(array('value' => Operation::OPERATION_UPDATE, 'service' => 'test.service', 'endpoint' => 'test.endpoint'));

        $this->assertEquals(Operation::OPERATION_UPDATE, $this->operation->operation);
        $this->assertEquals('test.service', $this->operation->service);
        $this->assertEquals('test.endpoint', $this->operation->endpoint);

        $this->operation = new Operation(array('value' => Operation::OPERATION_ALL, 'service' => 1000));

        $this->assertEquals(Operation::OPERATION_ALL, $this->operation->operation);
        $this->assertEquals('1000', $this->operation->service);
        $this->assertNull($this->operation->endpoint);
    }

    public function testDefaultOperations()
    {
        $expected = array(
            Operation::OPERATION_SAVE,
            Operation::OPERATION_UPDATE,
            Operation::OPERATION_DELETE,
            Operation::OPERATION_ALL
        );
        $this->assertEquals($expected, Operation::getOperationTypes());
    }
}
