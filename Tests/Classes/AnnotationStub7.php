<?php
/**
 * AnnotationStub7.php
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
 * Class AnnotationStub7
 *
 * @package TP\SolariumExtensionsBundle\Tests\Classes
 *
 * @Solarium\Document("solarium.client.default")
 */
class AnnotationStub7 extends AnnotationStub1
{
    /**
     * Constructor
     */
    public function __construct($invalidDate = false)
    {
        parent::__construct();

        if (!$invalidDate) {
            $this->collection = 'INVALID';
        } else {
            $this->date = 'INVALID';
        }

    }
}
