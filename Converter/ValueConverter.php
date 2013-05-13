<?php
/**
 * ValueConverter.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  2.0.1
 */
namespace TP\SolariumExtensionsBundle\Converter;

use Symfony\Component\PropertyAccess\PropertyAccessor;
use TP\SolariumExtensionsBundle\Converter\ValueConverterInterface;
use TP\SolariumExtensionsBundle\Metadata\PropertyMetadata;

/**
 * Class ValueConverter
 *
 * @package TP\SolariumExtensionsBundle\Converter
 */
class ValueConverter implements ValueConverterInterface
{
    public function convert($object, PropertyMetadata $property, PropertyAccessor $accessor)
    {
        $value = $object;

        if (!$property->multi) {
            $value = $property->getValue($object);
        }

        if ($property->propertyAccess && $property->propertyAccess !== PropertyMetadata::TYPE_RAW) {
            $value = $accessor->getValue(
                $value,
                $property->propertyAccess
            );
        }

        return $value;
    }

    /**
     * @param object $object
     * @param PropertyMetadata $property
     * @param PropertyAccessor $accessor
     *
     * @throws \InvalidArgumentException
     * @return array
     */
    public function convertMulti($object, PropertyMetadata $property, PropertyAccessor $accessor)
    {
        $traversable = $property->getValue($object);

        if (!is_array($traversable) && !$traversable instanceof \Traversable && !$traversable instanceof \stdClass) {
            $message = "Field '%s' is declared as multi field, but property value is not traversable.";

            throw new \InvalidArgumentException(sprintf($message, $property->name));
        }

        $values = array();

        foreach ($traversable as $item) {
            $values[] = $this->convert($item, $property, $accessor);
        }

        return $values;
    }
}
