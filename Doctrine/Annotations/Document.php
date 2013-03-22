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

use TP\SolariumExtensionsBundle\Doctrine\Annotations\Annotation as BaseAnnotation;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Field;

/**
 * Class Document
 *
 * @package TP\SolariumExtensionsBundle\Doctrine\Annotations
 * @Annotation
 */
class Document extends BaseAnnotation
{
    /**
     * The document boost that should be set to this document.
     *
     * @var double
     */
    public $boost = 0.0;

    /**
     * Holds an array of Operation annotations the Document should listen to.
     *
     * @var array
     */
    public $operations = array();

    /**
     * Constructor
     */
    public function __construct(Array $options)
    {
        if (isset($options['value'])) {
            $defaultOperation =  new Operation(
                array(
                    'value'     => Operation::OPERATION_ALL,
                    'service'   => (string) $options['value']
                )
            );
            $this->operations[Operation::OPERATION_ALL] = $defaultOperation;
        } else {
            $this->processOperationOption($options);
        }

        if (isset($options['boost'])) {
            if (!is_numeric($options['boost'])) {
                throw new \InvalidArgumentException("Parameter 'Boost' must be a numeric value.");
            }

            $this->boost = floatval($options['boost']);
        }
    }

    /**
     * @param string $operation
     *
     * @return null|Operation
     */
    public function getOperation($operation)
    {
        if (!$this->hasOperation($operation)) {
            return null;
        }

        return $this->operations[$operation];
    }

    /**
     * @param string $operation
     *
     * @return bool
     */
    public function hasOperation($operation)
    {
        return array_key_exists((string) $operation, $this->operations);
    }

    /**
     * @param array $options
     *
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    private function processOperationOption($options)
    {
        if (!isset($options['operations'])) {
            throw new \InvalidArgumentException("Required parameter 'operations' is missing");
        }

        if (!is_array($options['operations'])) {
            $type = gettype($options['operations']);
            $message = "Parameter 'operations' must be of type 'Array', '%s' given.";

            throw new \InvalidArgumentException(sprintf($message, $type));
        }

        foreach ($options['operations'] as $operation) {
            if (!$operation instanceof Operation) {
                $type    = gettype($operation);
                $message = "Operations must be of type 'Operation', '%s' given.";

                throw new \InvalidArgumentException(sprintf($message, $type));
            }

            if ($operation->operation === Operation::OPERATION_ALL  && count($options['operations']) > 1) {
                throw new \LogicException(
                    "You mustn't specify other Operations when using 'Operation::OPERATION_ALL'."
                );
            }

            if (array_key_exists($operation->operation, $this->operations)) {
                $message = "You have a duplicate Operation definition for '%s'.";

                throw new \LogicException(sprintf($message, $operation->operation));
            }

            $this->operations[$operation->operation] = $operation;
        }
    }
}
