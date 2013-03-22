<?php
/**
 * AnnotationStub8.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Tests\Classes;

use TP\SolariumExtensionsBundle\Doctrine\Annotations as Solarium;

use TP\SolariumExtensionsBundle\Tests\Classes\AnnotationStub1;

/**
 * Class AnnotationStub8
 *
 * @package TP\SolariumExtensionsBundle\Tests\Classes
 *
 * @Solarium\Document(
 *      operations={
 *          @Solarium\Operation("delete", service="solarium.client.delete")
 *      }
 * )
 */
class AnnotationStub8 extends AnnotationStub1
{
}
