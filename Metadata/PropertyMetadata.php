<?php
/**
 * PropertyMetadata.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Metadata;

use Metadata\PropertyMetadata as BasePropertyMetadata;

/**
 * Class PropertyMetadata
 *
 * @package TP\SolariumExtensionsBundle\Metadata
 */
class PropertyMetadata extends BasePropertyMetadata
{
    const TYPE_RAW = '__raw__';

    /**
     * @var string
     */
    public $fieldName;

    /**
     * @var string
     */
    public $type;

    /**
     * @var float
     */
    public $boost;

    /**
     * @var string
     */
    public $propertyAccess;

    /**
     * @var bool
     */
    public $multi;

    public function serialize()
    {
        return serialize(
            array(
                $this->class,
                $this->name,
                $this->fieldName,
                $this->type,
                $this->boost,
                $this->propertyAccess,
                $this->multi
            )
        );
    }

    public function unserialize($str)
    {
        list(
            $this->class,
            $this->name,
            $this->fieldName,
            $this->type,
            $this->boost,
            $this->propertyAccess,
            $this->multi
        ) = unserialize($str);

        $this->reflection = new \ReflectionProperty($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }
}
