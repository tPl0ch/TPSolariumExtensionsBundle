<?php
/**
 * ClassMetadata.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Metadata;

use Metadata\ClassMetadata as BaseClassMetadata;

/**
 * Class ClassMetadata
 *
 * @package TP\SolariumExtensionsBundle\Metadata
 */
class ClassMetadata extends BaseClassMetadata
{
    public $operations = array();
    public $mappingTable = array();
    public $boost = 0.0;
    public $id;

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
            $this->operations,
            $this->mappingTable,
            $this->boost,
            $this->id
        ));
    }

    /**
     * @param string $str
     */
    public function unserialize($str)
    {
        list(
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
            $this->operations,
            $this->mappingTable,
            $this->boost,
            $this->id
            ) = unserialize($str);

        $this->reflection = new \ReflectionClass($this->name);
    }
}
