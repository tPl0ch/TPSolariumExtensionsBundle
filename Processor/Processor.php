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

use Solarium\QueryType\Update\Query\Query;
use TP\SolariumExtensionsBundle\Manager\SolariumServiceManager;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation;
use TP\SolariumExtensionsBundle\Metadata\ClassMetadata;

use Symfony\Component\PropertyAccess\PropertyAccessor;
use TP\SolariumExtensionsBundle\Metadata\PropertyMetadata;

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

    /**
     * Processes the index for the given object and the given operation
     *
     * @param object $object
     * @param string $operation
     * @param bool   $instantCommit
     *
     * @return bool
     */
    public function process($object, $operation, $instantCommit = false)
    {
        if (!$this->needsProcessing($object, $operation)) {
            return false;
        }

        switch ($operation) {
            case Operation::OPERATION_SAVE:
                $this->processSave($object, false, $instantCommit);
                break;
            case Operation::OPERATION_UPDATE:
                $this->processSave($object, true, $instantCommit);
                break;
            case Operation::OPERATION_DELETE:
                $this->processDelete($object);
                break;
        }

        return true;
    }

    /**
     * @return void
     */
    public function flush()
    {
        $this->getServiceManager()->doUpdate();
    }

    /**
     * @param object $object
     * @param bool   $update
     * @param bool   $commitWithin
     *
     * @return void
     */
    protected function processSave($object, $update = true, $commitWithin = false)
    {
        $operation = Operation::OPERATION_SAVE;

        if (true === $update) {
            $operation = Operation::OPERATION_UPDATE;
        }

        $classMetadata = $this->getClassMetadata($object);

        $query    = $this->getServiceManager()->getUpdateQuery($classMetadata, $operation);
        $document = $query->createDocument();

        /** @var \Solarium\QueryType\Update\Query\Document\Document $document */
        $document->setBoost($classMetadata->boost);
        $document->addField($classMetadata->id, $object->getId());

        /** @var \TP\SolariumExtensionsBundle\Metadata\PropertyMetadata $property */
        foreach ($classMetadata->propertyMetadata as $property) {
            if (true === $property->multi) {
                $traversable = $property->getValue($object);

                if (!is_array($traversable) && !$traversable instanceof \Traversable && !$traversable instanceof \stdClass) {
                    $message = "Field '%s' is declared as multi field, but property value is not traversable.";

                    throw new \InvalidArgumentException(sprintf($message, $property->name));
                }
                
                foreach ($property->getValue($object) as $item) {
                    if ($property->propertyAccess === PropertyMetadata::TYPE_RAW) {
                        $value = $item;
                    } else {
                        $value = $this->getPropertyAccessor()->getValue($item, $property->propertyAccess);
                    }
                    $document->addField($property->fieldName, $value);
                }
            } else {
                $document->addField($property->fieldName, $property->getValue($object));
            }

            $document->setFieldBoost($property->fieldName, $property->boost);
        }

        $query->addDocument($document, $update, $commitWithin);
        $query->addCommit();
    }

    /**
     * Deletes a document from the index
     *
     * @param $object
     */
    protected function processDelete($object)
    {
        $classMetadata = $this->getClassMetadata($object);

        $query = $this->getServiceManager()->getUpdateQuery($classMetadata, Operation::OPERATION_DELETE);
        $query->addDeleteById($object->getId());
        $query->addCommit();
    }
}
