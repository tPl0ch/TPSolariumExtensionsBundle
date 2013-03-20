<?php
/**
 * AnnotationStub3.php
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
 * Class AnnotationStub
 *
 * @package TP\SolariumExtensionsBundle\Tests\Classes
 * @Solarium\Document({service={
        'save'='solarium.client.save',
 *      'delete'='solarium.client.delete',
 *      'update'='solarium.client.update'
 * }})
 */
class AnnotationStub3
{

}
