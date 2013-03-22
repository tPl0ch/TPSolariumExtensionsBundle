<?php
/**
 * IdTest.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Tests\Doctrine\Annotations;

use TP\SolariumExtensionsBundle\Doctrine\Annotations\Field;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Id;

/**
 * Class IdTest
 *
 * @package TP\SolariumExtensionsBundle\Tests\Doctrine\Annotations
 */
class IdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \TP\SolariumExtensionsBundle\Doctrine\Annotations\Id
     */
    public $id;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->id = new Id(array());
    }

    public function testDefaultName()
    {
        $this->assertEquals('id', $this->id->name);
        $this->assertEquals('id', $this->id->propertyAccess);
    }

    public function testValueSetting()
    {
        $this->id = new Id(array('value' => 'test'));
        $this->assertEquals('test', $this->id->name);
    }

    public function testNameSetting()
    {
        $this->id = new Id(array('name' => 'test'));
        $this->assertEquals('test', $this->id->name);
    }

    public function testValueBeforeName()
    {
        $this->id = new Id(array('value' => 'test', 'name' => 'test_two'));
        $this->assertEquals('test', $this->id->name);
    }

    public function testPropertyAccess()
    {
        $this->id = new Id(array('name' => 'test'));
        $this->assertEquals('test', $this->id->propertyAccess);

        $this->id = new Id(array('name' => 'test', 'propertyAccess' => 'access'));
        $this->assertEquals('access', $this->id->propertyAccess);
    }
}
