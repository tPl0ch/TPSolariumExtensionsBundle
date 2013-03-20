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

use TP\SolariumExtensionsBundle\Doctrine\Annotations\Annotation;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Field;

/**
 * Class Mapping
 *
 * @package TP\SolariumExtensionsBundle\Doctrine\Annotations
 */
class Mapping extends Annotation
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
     * Indicates if the mappings have already been merged
     *
     * @var bool
     */
    private $isMerged = false;

    /**
     * Holds the processed mapping array
     *
     * @var array
     */
    private $processedMapping = array();

    /**
     * Indicates if the mappings have been processed
     *
     * @var bool
     */
    private $isProcessed = false;

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
     * Processes a given mapping storage array, but checks all keys (Types) and drops invalid keys silently.
     * If strict checking is enabled, will raise an InvalidArgumentException instead on first invalid key.
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function getMappings()
    {
        if (!is_array($this->mapping)) {
            $type    = gettype($this->mapping);
            $message = "Invalid type for 'mapping' given. Expected 'Array', but got '%s'";

            throw new \InvalidArgumentException(sprintf($message, $type));
        }

        foreach (array_keys($this->mapping) as $type) {
            if ($this->isValidType($type)) {
                $this->processedMapping[$type] = (string) $this->mapping[$type];
            } else {
                if ((bool) $this->strict) {
                    $message = "Invalid field type '%s' found in 'mapping' and strict checking is enabled.";

                    throw new \InvalidArgumentException(sprintf($message, $type));
                }
            }
        }

        $this->mergeMappings(true);
        $this->isProcessed = true;

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
        if (!$this->isProcessed) {
            $this->getMappings();
        }

        if (!$this->isValidType($type)) {
            $message = "Invalid Field type '%s' given. Only %s are allowed.";

            throw new \InvalidArgumentException(sprintf($message, $type, implode(',', Field::getFieldTypes())));
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
        $this->isMerged    = false;
        $this->isProcessed = false;
        $this->mapping     = self::$defaultMappingStorage;

        return $this;
    }

    /**
     * Merges the custom and the default mappings and sets a flag.
     *
     * @param bool $force
     *
     * @return array
     */
    private function mergeMappings($force = false)
    {
        if ($force || !$this->isMerged) {
            $this->mapping = array_merge(self::$defaultMappingStorage, $this->processedMapping);

            $this->isMerged = true;
        }

        return $this->mapping;
    }
}
