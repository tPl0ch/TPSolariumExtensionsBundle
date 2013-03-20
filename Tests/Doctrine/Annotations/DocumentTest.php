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

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->document = new Document(array(
            'service' => 'test.service'
        ));
    }

    /**
     * @param string $type
     *
     * @dataProvider getOperationTypes
     */
    public function testServiceParameterString($operation)
    {
        $this->assertEquals('test.service', $this->document->getService($operation));
    }

    /**
     * Tests that array service parameter works as expected
     */
    public function testServiceParameterArray()
    {
        $this->document->service = array(
            Document::OPERATION_SAVE   => 'test.save',
            Document::OPERATION_UPDATE => 'test.update',
            Document::OPERATION_DELETE => 'test.delete'
        );

        $this->assertEquals('test.save', $this->document->getService(Document::OPERATION_SAVE));
        $this->assertEquals('test.update', $this->document->getService(Document::OPERATION_UPDATE));
        $this->assertEquals('test.delete', $this->document->getService(Document::OPERATION_DELETE));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The 'service' parameter is required.
     */
    public function testServiceParameterRequired()
    {
        $this->document->service = null;
        $this->document->getService(Document::OPERATION_SAVE);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The operation 'INVALID' is invalid. Only save,update,delete are supported.
     */
    public function testGetServiceInvalidType()
    {
        $this->document->getService('INVALID');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The specified service for operation 'save' is invalid. Expected 'Array' or 'String', 'double' given.
     */
    public function testInvalidServiceParameter()
    {
        $this->document->service = 12.23;
        $this->document->getService(Document::OPERATION_SAVE);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The service array lacks the service definition for operation 'delete'.
     */
    public function testServiceParameterArrayIncomplete()
    {
        $this->document->service = array(
            Document::OPERATION_SAVE   => 'test.save',
            Document::OPERATION_UPDATE => 'test.update'
        );
        $this->document->getService(Document::OPERATION_DELETE);
    }

    /**
     * Tests that the default operations are correct
     */
    public function testDefaultOperations()
    {
        $expected = array(Document::OPERATION_SAVE, Document::OPERATION_UPDATE, Document::OPERATION_DELETE);
        $this->assertEquals($expected, Document::getOperationTypes());
    }

    /**
     * Tests that the operations parameter setting and getting works as expected
     */
    public function testOperationsParameter()
    {
        $expected = array(Document::OPERATION_SAVE, Document::OPERATION_UPDATE);
        $this->document->operations = $expected;
        $this->assertEquals($expected, $this->document->getOperations());

        $this->document->operations = array('INVALID', Document::OPERATION_SAVE, Document::OPERATION_UPDATE);
        $this->assertEquals($expected, $this->document->getOperations());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid type for key 'operations'. Expected 'Array', but got 'integer'.
     */
    public function testInvalidOperationsType()
    {
        $this->document = new Document(array(
            'operations' => 24
        ));
        $this->document->getOperations();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The 'operation' array contains invalid keys and strict checking is enabled.
     */
    public function testInvalidOperationEntryStrict()
    {
        $this->document->operations = array('INVALID', Document::OPERATION_SAVE, Document::OPERATION_UPDATE);
        $this->document->getOperations(true);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage No valid operation was specified in 'operations'. Valid operations are save,update,delete.
     */
    public function testOperationsEmpty()
    {
        $this->document->operations = array('INVALID', 'ANOTHER_INVALID');
        $this->document->getOperations();
    }

    /**
     * Tests that the boost value is converted from numeric to float
     */
    public function testGetBoost()
    {
        $this->document->boost = 1;
        $this->assertEquals(1.0, $this->document->getBoost());

        $this->document->boost = '1';
        $this->assertEquals(1.0, $this->document->getBoost());

        $this->document->boost = 2.4;
        $this->assertEquals(2.4, $this->document->getBoost());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid type for 'boost' value. Expected 'numeric', got 'array'.
     */
    public function testInvalidBoost()
    {
        $this->document->boost = array();
        $this->document->getBoost();
    }

    /**
     * Tests that valid and invalid operations are recognized
     */
    public function testIsValidOperation()
    {
        $this->assertTrue($this->document->isValidOperation(Document::OPERATION_SAVE));
        $this->assertTrue($this->document->isValidOperation(Document::OPERATION_DELETE));
        $this->assertTrue($this->document->isValidOperation(Document::OPERATION_UPDATE));
        $this->assertFalse($this->document->isValidOperation('INVALID'));
    }

    /**
     * Tests that valid field types are recognized.
     *
     * @dataProvider getFieldTypes
     */
    public function testIsValidFieldType($type)
    {
        $this->assertTrue($this->document->isValidType($type));
    }

    /**
     * Tests that invalid Field types are recognized
     */
    public function testIsValidFieldTypeInvalid()
    {
        $this->assertFalse($this->document->isValidType('INVALID'));
    }

    /**
     * @return array
     */
    public function getFieldTypes()
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
    public function getOperationTypes()
    {
        $return = array();
        foreach (Document::getOperationTypes() as $type) {
            $return[] = array($type);
        }

        return $return;
    }
}
