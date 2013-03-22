<?php
/**
 * AnnotationStub4.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Tests\Classes;

use Doctrine\Common\Collections\ArrayCollection;
use TP\SolariumExtensionsBundle\Doctrine\Annotations as Solarium;

/**
 * Class AnnotationStub4
 *
 * @package TP\SolariumExtensionsBundle\Tests\Classes
 *
 * @Solarium\Document("solarium.client.default")
 */
class AnnotationStub4
{
    /**
     * @var int
     *
     * @Solarium\Id()
     */
    public $id = 1423;

    /**
     * @var int
     *
     * @Solarium\Id()
     */
    public $duplicateId = 1423;
}
