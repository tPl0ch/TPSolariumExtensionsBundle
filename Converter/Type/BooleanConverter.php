<?php
/**
 * BooleanConverter.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  2.0.1
 */
namespace TP\SolariumExtensionsBundle\Converter\Type;

use Symfony\Component\PropertyAccess\PropertyAccessor;
use TP\SolariumExtensionsBundle\Metadata\PropertyMetadata;
use TP\SolariumExtensionsBundle\Converter\ValueConverter;

/**
 * Class BooleanConverter
 *
 * @package TP\SolariumExtensionsBundle\Converter
 */
class BooleanConverter extends ValueConverter
{
    /**
     * @param object $object
     * @param PropertyMetadata $property
     * @param PropertyAccessor $accessor
     *
     * @return mixed|object|string
     *
     * @throws \InvalidArgumentException
     */
    public function convert($object, PropertyMetadata $property, PropertyAccessor $accessor)
    {
        return (bool) parent::convert($object, $property, $accessor);
    }
}
