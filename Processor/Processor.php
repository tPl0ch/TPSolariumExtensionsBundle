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
     * @var \TP\SolariumExtensionsBundle\Metadata\ClassMetadata
     */
    private $currentOperation;

    /**
     * @var array
     */
    private $commitStack;

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
        $this->commitStack      = array();
        $this->currentOperation = null;
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
        return $this->getMetadataFactory()->getMetadataForClass($entity);
    }

    /**
     * @return PropertyAccessor
     */
    public function getPropertyAccessor()
    {
        return $this->propertyAccessor;
    }
}
