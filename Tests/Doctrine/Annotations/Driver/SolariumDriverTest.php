<?php
/**
 * SolariumDriverTest.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Tests\Doctrine\Annotations\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub1;

/**
 * Class SolariumDriverTest
 *
 * @package TP\SolariumExtensionsBundle\Tests\Doctrine\Annotations\Driver
 */
class SolariumDriverTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->reader = new AnnotationReader();

        $class = new AnnotationStub1();

        print_r($this->reader->getClassAnnotation(
            new \ReflectionClass($class),
            'TP\SolariumExtensionsBundle\Doctrine\Annotations\Document'
        ));

    }

    public function testEmpty()
    {

    }
}
