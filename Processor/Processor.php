<?php
/**
 * Processor.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Processor;

use Doctrine\Common\Annotations\AnnotationReader;

use Metadata\MetadataFactoryInterface;

use TP\SolariumExtensionsBundle\Manager\SolariumServiceManager;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation;
use TP\SolariumExtensionsBundle\Metadata\ClassMetadata;

use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class Processor
 *
 * @package TP\SolariumExtensionsBundle\Processor
 */
class Processor
{
    /**
     * @var \Metadata\MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var \TP\SolariumExtensionsBundle\Manager\SolariumServiceManager
     */
    private $serviceManager;

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @param MetadataFactoryInterface $metadataFactory
     * @param SolariumServiceManager $serviceManager
     * @param PropertyAccessor $propertyAccessor
     */
    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        SolariumServiceManager $serviceManager,
        PropertyAccessor $propertyAccessor
    )
    {
        $this->metadataFactory  = $metadataFactory;
        $this->serviceManager   = $serviceManager;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @return \Metadata\MetadataFactoryInterface
     */
    public function getMetadataFactory()
    {
        return $this->metadataFactory;
    }

    /**
     * @return \TP\SolariumExtensionsBundle\Manager\SolariumServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * @param object $entity
     *
     * @return ClassMetadata|null
     */
    public function getClassMetadata($entity)
    {
        return $this->getMetadataFactory()
            ->getMetadataForClass(get_class($entity))
            ->getOutsideClassMetadata()
        ;
    }

    /**
     * @return PropertyAccessor
     */
    public function getPropertyAccessor()
    {
        return $this->propertyAccessor;
    }

    /**
     * Checks if a given object needs processing for a given operation
     *
     * @param object $object
     * @param string $operation
     *
     * @return bool
     */
    public function needsProcessing($object, $operation)
    {
        return $this->getClassMetadata($object)
            ->hasOperation($operation)
        ;
    }
}
