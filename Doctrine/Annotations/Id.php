<?php
/**
 * Id.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Doctrine\Annotations;

use TP\SolariumExtensionsBundle\Doctrine\Annotations\Annotation as BaseAnnotation;

/**
 * Class Id
 *
 * @package TP\SolariumExtensionsBundle\Doctrine\Annotations
 * @Annotation
 */
class Id extends BaseAnnotation
{
    /**
     * Holds the default ID field for the Document
     *
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $propertyAccess;

    /**
     * @param array $options
     */
    public function __construct(Array $options)
    {
        if (isset($options['value'])) {
            $this->name = (string) $options['value'];
        } elseif (isset($options['name'])) {
            $this->name = (string) $options['name'];
        } else {
            $this->name = 'id';
        }

        if (isset($options['propertyAccess'])) {
            $this->propertyAccess = (string) $options['propertyAccess'];
        } else {
            $this->propertyAccess = $this->name;
        }
    }
}
