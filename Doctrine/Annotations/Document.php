<?php
/**
 * Document.php
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
 * Class Document
 *
 * @package TP\SolariumExtensionsBundle\Doctrine\Annotations
 * @Annotation
 */
class Document extends Annotation
{
    /**
     * Operation constants
     */
    const OPERATION_DELETE = 'delete';
    const OPERATION_UPDATE = 'update';
    const OPERATION_SAVE   = 'save';

    /**
     * @return array
     */
    public static function getOperationTypes()
    {
        return array(
            self::OPERATION_SAVE,
            self::OPERATION_UPDATE,
            self::OPERATION_DELETE
        );
    }

    /**
     * The document boost that should be set to this document.
     *
     * @var int
     */
    public $boost = 0;

    /**
     * Holds the service id(s) of the nelmio solarium service to use.
     *
     * If you provide a string, the given client service will be used for all operations.
     *
     * If you provide an array with the format: [ operation => service_id, another_operation => another_service_id ],
     * the corresponding services will be used for a given operation.
     *
     * @var string|array
     */
    public $service;

    /**
     * Holds an array of operations the Document should listen to.
     * Defaults to ['save', 'update', 'delete'].
     *
     * @var array
     */
    public $operations = array(self::OPERATION_SAVE, self::OPERATION_UPDATE, self::OPERATION_DELETE);

    /**
     * Gets the service from the service definition
     *
     * @param string $operation
     *
     * @return array|string
     *
     * @throws \InvalidArgumentException
     */
    public function getService($operation)
    {
        if ($this->service === null) {
            throw new \InvalidArgumentException("The 'service' parameter is required.");
        }

        if (!$this->isValidOperation($operation)) {
            $message = "The operation '%s' is invalid. Only %s are supported.";

            throw new \InvalidArgumentException(
                sprintf($message, $operation, implode(',', $this->getOperations())));
        }

        if (!is_array($this->service) && !is_string($this->service)) {
            $type    = gettype($this->service);
            $message = "The specified service for operation '%s' is invalid. " .
                "Expected 'Array' or 'String', '%s' given.";

            throw new \InvalidArgumentException(sprintf($message, $operation, $type));
        }

        if (!is_array($this->service)) {
            return $this->service;
        }

        if (!isset($this->service[$operation])) {
            $message = "The service array lacks the service definition for operation '%s'.";

            throw new \InvalidArgumentException(sprintf($message, $operation, $operation));
        }

        return $this->service[$operation];
    }

    /**
     * @param bool $strict
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function getOperations($strict = false)
    {
        if (!is_array($this->operations)) {
            $type    = gettype($this->operations);
            $message = "Invalid type for key 'operations'. Expected 'Array', but got '%s'.";

            throw new \InvalidArgumentException(sprintf($message, $type));
        }

        if ($strict && count(array_diff($this->operations, self::getOperationTypes())) > 0) {
            throw new \InvalidArgumentException(
                "The 'operation' array contains invalid keys and strict checking is enabled.");
        }

        $this->operations = array_values(array_intersect($this->operations, self::getOperationTypes()));

        if (empty($this->operations)) {
            $message = "No valid operation was specified in 'operations'. Valid operations are %s.";

            throw new \InvalidArgumentException(sprintf($message, implode(',', self::getOperationTypes())));
        }

        return $this->operations;
    }

    /**
     * @return float
     *
     * @throws \InvalidArgumentException
     */
    public function getBoost()
    {
        if (!is_numeric($this->boost)) {
            $type    = gettype($this->boost);
            $message = "Invalid type for 'boost' value. Expected 'numeric', got '%s'.";

            throw new \InvalidArgumentException(sprintf($message, $type));
        }

        return floatval($this->boost);
    }
}
