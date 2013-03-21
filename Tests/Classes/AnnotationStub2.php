<?php
/**
 * AnnotationStub2.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Tests\Classes;

use TP\SolariumExtensionsBundle\Doctrine\Annotations as Solarium;

/**
 * Class AnnotationStub2
 *
 * @package TP\SolariumExtensionsBundle\Tests\Classes
 *
 * @package TP\SolariumExtensionsBundle\Tests\Classes
 *
 * @Solarium\Document(
 *      operations={
 *          @Solarium\Operation("delete", service="solarium.client.delete"),
 *          @Solarium\Operation("update", service="solarium.client.update"),
 *          @Solarium\Operation("save", service="solarium.client.save")
 *      },
 *      boost="2.4"
 * )
 */
class AnnotationStub2
{

}
