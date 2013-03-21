<?php
/**
 * Mapping.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Doctrine\Annotations;

use TP\SolariumExtensionsBundle\Doctrine\Annotations\Annotation as BaseAnnotation;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Field;

/**
 * Class Mapping
 *
 * @package TP\SolariumExtensionsBundle\Doctrine\Annotations
 * @Annotation
 */
class Mapping extends BaseAnnotation
{
    /**
     * Mapping storage for default dynamic fields definitions.
     *
     * @var array
     */
    private static $defaultMappingStorage = array(
        Field::TYPE_INT           => '_i',
        Field::TYPE_INT_MULTI     => '_is',
        Field::TYPE_STRING        => '_s',
        Field::TYPE_STRING_MULTI  => '_ss',
        Field::TYPE_LONG          => '_l',
        Field::TYPE_LONG_MULTI    => '_ls',
        Field::TYPE_TEXT          => '_t',
        Field::TYPE_TEXT_MULTI    => '_txt',
        Field::TYPE_BOOLEAN       => '_b',
        Field::TYPE_BOOLEAN_MULTI => '_bs',
        Field::TYPE_FLOAT         => '_f',
        Field::TYPE_FLOAT_MULTI   => '_fs',
        Field::TYPE_DOUBLE        => '_d',
        Field::TYPE_DOUBLE_MULTI  => '_ds',
        Field::TYPE_DATE          => '_dt',
        Field::TYPE_DATE_MULTI    => '_dts',
        Field::TYPE_LOCATION      => '_p',
    );

    /**
     * @return array
     */
    public static function getDefaultMapping()
    {
        return self::$defaultMappingStorage;
    }

    /**
     * Contains the custom field type mapping for this Document
     *
     * @var array
     */
    public $mapping = array();

    /**
     * Raises an Exception instead of silently dropping invalid field types.
     *
     * @var bool
     */
    public $strict = false;

    /**
     * @param array $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(Array $options)
    {
        if (isset($options['strict'])) {
            $this->strict = (bool) $options['strict'];
        }

        if (isset($options['mapping'])) {
            if (!is_array($options['mapping'])) {
                $type    = gettype($options['mapping']);
                $message = "Invalid type for 'mapping' given. Expected 'Array', but got '%s'";

                throw new \InvalidArgumentException(sprintf($message, $type));
            }

            foreach (array_keys($options['mapping']) as $type) {
                if ($this->isValidType($type)) {
                    $this->mapping[$type] = $options['mapping'][$type];
                } else {
                    if ($this->strict === true) {
                        $message = "Invalid field type '%s' found in 'mapping' and strict checking is enabled.";

                        throw new \InvalidArgumentException(sprintf($message, $type));
                    }
                }
            }

            $this->mapping = array_merge(self::$defaultMappingStorage, $this->mapping);
        } else {
            $this->mapping = self::$defaultMappingStorage;
        }
    }

    /**
     * Processes a given mapping storage array, but checks all keys (Types) and drops invalid keys silently.
     * If strict checking is enabled, will raise an InvalidArgumentException instead on first invalid key.
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function getMappings()
    {
        return $this->mapping;
    }

    /**
     * @param string $type
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function getMapping($type)
    {
        if (!$this->isValidType($type)) {
            return null;
        }

        return $this->mapping[$type];
    }

    /**
     * Restores the mapping to its defaults
     *
     * @return Document
     */
    public function restoreMapping()
    {
        $this->mapping = self::$defaultMappingStorage;

        return $this;
    }
}
