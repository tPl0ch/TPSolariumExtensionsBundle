<?php
/**
 * Operation.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Doctrine\Annotations;

use TP\SolariumExtensionsBundle\Doctrine\Annotations\Field;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Annotation as BaseAnnotation;

/**
 * Class Operation
 *
 * @package TP\SolariumExtensionsBundle\Doctrine\Annotations
 * @Annotation
 */
class Operation extends BaseAnnotation
{
    /**
     * Operation constants
     */
    const OPERATION_DELETE = 'delete';
    const OPERATION_UPDATE = 'update';
    const OPERATION_SAVE   = 'save';
    const OPERATION_ALL    = 'all';

    /**
     * @return array
     */
    public static function getOperationTypes()
    {
        return array(
            self::OPERATION_SAVE,
            self::OPERATION_UPDATE,
            self::OPERATION_DELETE,
            self::OPERATION_ALL
        );
    }

    /**
     * @var string
     */
    public $service;

    /**
     * @var string
     */
    public $operation;

    /**
     * @var string
     */
    public $endpoint;

    /**
     * @param array $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(Array $options)
    {
        if (!isset($options['value'])) {
            throw new \InvalidArgumentException("Required operation type for 'Operation' Annotation is missing.");
        }

        if (!$this->isValidOperation($options['value'])) {
            $message = "Invalid operation value '%s'. Only %s are supported.";

            throw new \InvalidArgumentException(
                sprintf($message, $options['value'], implode(',', self::getOperationTypes()))
            );
        }

        $this->operation = $options['value'];

        if (!isset($options['service'])) {
            $message = "Required 'service' parameter for operation '%s' is missing.";

            throw new \InvalidArgumentException(sprintf($message, $this->operation));
        }

        if (isset($options['endpoint'])) {
            $this->endpoint = (string) $options['endpoint'];
        }

        $this->service = (string) $options['service'];
    }
}
