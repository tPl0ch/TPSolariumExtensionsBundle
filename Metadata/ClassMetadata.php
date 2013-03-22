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
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation;

/**
 * Class ClassMetadata
 *
 * @package TP\SolariumExtensionsBundle\Metadata
 */
class ClassMetadata extends BaseClassMetadata
{
    /**
     * @var array
     */
    public $operations = array();

    /**
     * @var array
     */
    public $mappingTable = array();

    /**
     * @var float
     */
    public $boost = 0.0;

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $idPropertyAccess;

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(
            array(
                $this->name,
                $this->methodMetadata,
                $this->propertyMetadata,
                $this->fileResources,
                $this->createdAt,
                $this->operations,
                $this->mappingTable,
                $this->boost,
                $this->id,
                $this->idPropertyAccess
            )
        );
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
            $this->id,
            $this->idPropertyAccess
            ) = unserialize($str);

        $this->reflection = new \ReflectionClass($this->name);
    }

    /**
     * @param string $operation
     *
     * @return bool
     */
    public function hasOperation($operation)
    {
        if (array_key_exists(Operation::OPERATION_ALL, $this->operations)) {
            return true;
        }

        return array_key_exists($operation, $this->operations);
    }

    /**
     * @param string $operation
     *
     * @return null
     */
    public function getServiceId($operation)
    {
        if (!$this->hasOperation($operation)) {
            return null;
        }

        if ($this->hasOperation(Operation::OPERATION_ALL)) {
            return $this->operations[Operation::OPERATION_ALL];
        }

        return $this->operations[$operation];
    }
}
