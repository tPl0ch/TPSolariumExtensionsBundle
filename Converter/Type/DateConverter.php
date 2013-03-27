<?php
/**
 * DateConverter.php
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
 * Class DateConverter
 *
 * @package TP\SolariumExtensionsBundle\Converter
 */
class DateConverter extends ValueConverter
{
    /**
     * @param \DateTime $date
     *
     * @return string
     */
    public static function makeSolrTime(\DateTime $date)
    {
        $date->setTimezone(new \DateTimeZone('UTC'));

        return $date->format('Y-m-d\TH:i:s\Z');
    }

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

        if (!$value instanceof \DateTime) {
            $type    = gettype($value);
            $message = "Property '%s' must be of type \\DateTime, '%s' given.";

            throw new \InvalidArgumentException(sprintf($message, $property->name, $type));
        }

        return self::makeSolrTime($value);
    }
}
