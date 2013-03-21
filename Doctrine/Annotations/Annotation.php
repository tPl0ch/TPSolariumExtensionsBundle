<?php
/**
 * Annotation.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Doctrine\Annotations;

use Doctrine\Common\Annotations\Annotation as BaseAnnotation;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Field;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation;

/**
 * Class Annotation
 *
 * @package TP\SolariumExtensionsBundle\Doctrine\Annotations
 */
class Annotation
{
    /**
     * Checks if a given field type is multiValued
     *
     * @param string $type
     *
     * @return bool
     */
    public function isMultiValuedType($type)
    {
        return in_array((string) $type, Field::getMultiFieldTypes());
    }

    /**
     * Checks if a given field type is valid.
     *
     * @param string $type
     *
     * @return Boolean
     */
    public function isValidType($type)
    {
        return in_array((string) $type, Field::getFieldTypes());
    }

    /**
     * @param string $operation
     *
     * @return bool
     */
    public function isValidOperation($operation)
    {
        return in_array((string) $operation, Operation::getOperationTypes());
    }
}
