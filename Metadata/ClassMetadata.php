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
    public $endpoints = array();

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
                $this->endpoints,
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
            $this->endpoints,
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
        return $this->operationExistsForKey($operation, 'operations');
    }

    /**
     * @param string $operation
     *
     * @return bool
     */
    public function hasEndpoint($operation)
    {
        return $this->operationExistsForKey($operation, 'endpoints');
    }

    /**
     * @param string $operation
     *
     * @return null
     */
    public function getServiceId($operation)
    {
        return $this->getOperationValueForKey($operation, 'operations');
    }

    /**
     * @param string $operation
     *
     * @return null|string
     */
    public function getEndpoint($operation)
    {
        return $this->getOperationValueForKey($operation, 'endpoints');
    }

    /**
     * @param string $operation
     * @param string $key
     *
     * @return null|string
     */
    private function getOperationValueForKey($operation, $key)
    {
        if (!$this->operationExistsForKey($operation, $key)) {
            return null;
        }

        if ($this->operationExistsForKey(Operation::OPERATION_ALL, $key)) {
            return $this->{$key}[Operation::OPERATION_ALL];
        }

        return $this->{$key}[$operation];
    }

    /**
     * @param string $operation
     * @param string $key
     *
     * @return bool
     */
    private function operationExistsForKey($operation, $key)
    {
        if (array_key_exists(Operation::OPERATION_ALL, $this->{$key})) {
            return true;
        }

        return array_key_exists($operation, $this->{$key});
    }
}
