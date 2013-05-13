<?php
/**
 * FloatConverter.php
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
 * Class FloatConverter
 *
 * @package TP\SolariumExtensionsBundle\Converter
 */
class FloatConverter extends ValueConverter
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
        $value = parent::convert($object, $property, $accessor);

        if (!is_numeric($value)) {
            $type    = gettype($value);
            $message = "Property '%s' must be a numeric value, '%s' given.";

            throw new \InvalidArgumentException(sprintf($message, $property->name, $type));
        }

        return (float) $value;
    }
}
