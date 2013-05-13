<?php
/**
 * ConverterCollection.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  2.0.1
 */
namespace TP\SolariumExtensionsBundle\Converter;

use TP\SolariumExtensionsBundle\Doctrine\Annotations\Field;

/**
 * Class ConverterCollection
 *
 * @package TP\SolariumExtensionsBundle\Converter
 */
class ConverterCollection
{
    /**
     * @var array
     */
    private $converters;

    /**
     * @var array
     */
    private $objectMap;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->converters = array();
        $this->objectMap  = array();
    }

    /**
     * @param string $class
     *
     * @return ValueConverterInterface
     *
     * @throws \InvalidArgumentException
     */
    private function createConverter($class)
    {
        $object = new $class;

        if (!$object instanceof ValueConverterInterface) {
            throw new \InvalidArgumentException(
                "Class '$class' must implement 'TP\\SolariumExtensionsBundle\\Converter\\ValueConverterInterface'"
            );
        }

        return $object;
    }

    /**
     * @param string $type
     * @param string $class
     *
     * @return $this
     */
    public function registerConverter($type, $class)
    {
        if (!array_key_exists($class, $this->objectMap)) {
            $converter = $this->createConverter($class);

            $this->objectMap[$class] = $converter;
        }

        $this->converters[$type] = $class;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return ValueConverterInterface
     */
    public function getConverter($type)
    {
        if (!$this->hasConverter($type)) {
            return null;
        }

        return $this->objectMap[$this->converters[$type]];
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function hasConverter($type)
    {
        return array_key_exists($type, $this->converters);
    }
}
