<?php
/**
 * ValueConverterInterface.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  2.0.1
 */
namespace TP\SolariumExtensionsBundle\Converter;

use Symfony\Component\PropertyAccess\PropertyAccessor;
use TP\SolariumExtensionsBundle\Metadata\PropertyMetadata;

/**
 * interface ValueConverterInterface
 *
 * @package TP\SolariumExtensionsBundle\Converter
 */
interface ValueConverterInterface
{
    /**
     * @param object $object
     * @param PropertyMetadata $property
     * @param PropertyAccessor $accessor
     *
     * @return mixed
     */
    public function convert($object, PropertyMetadata $property, PropertyAccessor $accessor);

    /**
     * @param object $object
     * @param PropertyMetadata $property
     * @param PropertyAccessor $accessor
     *
     * @return mixed
     */
    public function convertMulti($object, PropertyMetadata $property, PropertyAccessor $accessor);
}
