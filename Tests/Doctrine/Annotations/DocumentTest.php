<?php
/**
 * DocumentTest.php
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
 * Class DocumentTest
 *
 * @package TP\SolariumExtensionsBundle\Tests\Doctrine\Annotations
 */
class DocumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \TP\SolariumExtensionsBundle\Doctrine\Annotations\Document
     */
    public $document;

    public function testHasOperation()
    {
        $this->document = new Document(array(
            'operations' => array(
                new Operation(array('value' => Operation::OPERATION_SAVE, 'service' => 'test.all')),
                new Operation(array('value' => Operation::OPERATION_UPDATE, 'service' => 'test.update'))
            )
        ));

        $this->assertTrue($this->document->hasOperation(Operation::OPERATION_SAVE));
        $this->assertTrue($this->document->hasOperation(Operation::OPERATION_UPDATE));

        $this->assertFalse($this->document->hasOperation(Operation::OPERATION_DELETE));
        $this->assertFalse($this->document->hasOperation(Operation::OPERATION_ALL));
        $this->assertFalse($this->document->hasOperation('INVALID'));
    }

    public function testDefaultServiceForAll()
    {
        $this->document = new Document(array(
            'value' => 'test.service'
        ));

        $expected = new Operation(array(
            'value'     => Operation::OPERATION_ALL,
            'service'   => 'test.service'
        ));

        $this->assertEquals($expected, $this->document->getOperation(Operation::OPERATION_ALL));
    }

    public function testGetOperation()
    {
        $operationOne = new Operation(array('value' => Operation::OPERATION_SAVE, 'service' => 'test.all'));
        $operationTwo = new Operation(array('value' => Operation::OPERATION_UPDATE, 'service' => 'test.update'));

        $this->document = new Document(array(
            'operations' => array(
                $operationOne,
                $operationTwo
            )
        ));

        $this->assertSame($operationOne, $this->document->getOperation(Operation::OPERATION_SAVE));
        $this->assertSame($operationTwo, $this->document->getOperation(Operation::OPERATION_UPDATE));

        $this->assertNull($this->document->getOperation(Operation::OPERATION_DELETE));
    }

    public function testBoostValueConversion()
    {
        $this->document = new Document(array(
            'operations' => array(
                new Operation(array('value' => Operation::OPERATION_ALL, 'service' => 'test.service'))
            ),
            'boost' => '124'
        ));

        $this->assertEquals(124.0, $this->document->boost);

        $this->document = new Document(array(
            'operations' => array(
                new Operation(array('value' => Operation::OPERATION_ALL, 'service' => 'test.service'))
            ),
            'boost' => 32
        ));
        $this->assertEquals(32.0, $this->document->boost);

        $this->document = new Document(array(
            'operations' => array(
                new Operation(array('value' => Operation::OPERATION_ALL, 'service' => 'test.service'))
            ),
            'boost' => 24.35
        ));
        $this->assertEquals(24.35, $this->document->boost);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Required parameter 'operations' is missing
     */
    public function testMissingOperationsException()
    {
        $this->document = new Document(array());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Parameter 'operations' must be of type 'Array', 'string' given.
     */
    public function testOperationsInvalidType()
    {
        $this->document = new Document(array('operations' => 'foo'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Operations must be of type 'Operation', 'object' given.
     */
    public function testOperationInvalidType()
    {
        $this->document = new Document(array('operations' => array(
            new \stdClass()
        )));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Parameter 'Boost' must be a numeric value.
     */
    public function testInvalidBoostValue()
    {
        $this->document = new Document(array(
            'operations' => array(
                new Operation(array('value' => Operation::OPERATION_ALL, 'service' => 'test.service'))
            ),
            'boost' => 'foo'
        ));
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage You mustn't specify other Operations when using 'Operation::OPERATION_ALL'.
     */
    public function testLogicExceptionAllOperationOnly()
    {
        $this->document = new Document(array(
            'operations' => array(
                new Operation(array('value' => Operation::OPERATION_ALL, 'service' => 'test.service')),
                new Operation(array('value' => Operation::OPERATION_UPDATE, 'service' => 'test.service'))
            )
        ));
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage You have a duplicate Operation definition for 'update'.
     */
    public function testLogicExceptionDuplicateOperation()
    {
        $this->document = new Document(array(
            'operations' => array(
                new Operation(array('value' => Operation::OPERATION_UPDATE, 'service' => 'test.service')),
                new Operation(array('value' => Operation::OPERATION_UPDATE, 'service' => 'test.service'))
            )
        ));
    }
}
