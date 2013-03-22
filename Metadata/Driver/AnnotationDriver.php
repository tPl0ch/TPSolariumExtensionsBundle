<?php
/**
 * AnnotationDriver.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;

use TP\SolariumExtensionsBundle\Doctrine\Annotations\Document;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Field;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Mapping;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Id;

use TP\SolariumExtensionsBundle\Metadata\PropertyMetadata;
use TP\SolariumExtensionsBundle\Metadata\ClassMetadata;

use Metadata\Driver\DriverInterface;

/**
 * Class AnnotationDriver
 *
 * @package TP\SolariumExtensionsBundle\Doctrine\Annotations
 */
class AnnotationDriver implements DriverInterface
{
    const ANNOTATION_DOCUMENT  = 'TP\SolariumExtensionsBundle\Doctrine\Annotations\Document';
    const ANNOTATION_OPERATION = 'TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation';
    const ANNOTATION_FIELD     = 'TP\SolariumExtensionsBundle\Doctrine\Annotations\Field';
    const ANNOTATION_ID        = 'TP\SolariumExtensionsBundle\Doctrine\Annotations\Id';
    const ANNOTATION_MAPPING   = 'TP\SolariumExtensionsBundle\Doctrine\Annotations\Mapping';

    /**
     * @return array
     */
    public static function getClassAnnotationClasses()
    {
        return array(
            self::ANNOTATION_DOCUMENT,
            self::ANNOTATION_MAPPING
        );
    }

    /**
     * @return array
     */
    public static function getPropertyAnnotationClasses()
    {
        return array(
            self::ANNOTATION_FIELD,
            self::ANNOTATION_ID
        );
    }

    /**
     * @var \Doctrine\Common\Annotations\AnnotationReader
     */
    private $reader;

    /**
     * @param AnnotationReader       $reader
     */
    public function __construct(AnnotationReader $reader)
    {
        $this->reader  = $reader;
    }

    /**
     * @param \ReflectionClass $class
     *
     * @throws \LogicException
     * @return \Metadata\ClassMetadata
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new ClassMetadata($class->getName());

        /** @var Document $annotation */
        $annotation = $this->reader->getClassAnnotation(
            $classMetadata->reflection,
            self::ANNOTATION_DOCUMENT
        );

        if (null === $annotation) {
            return $classMetadata;
        }

        $this->setDocumentDataToClassMetadata($annotation, $classMetadata);

        /** @var Mapping $annotation */
        $annotation = $this->reader->getClassAnnotation(
            $classMetadata->reflection,
            self::ANNOTATION_MAPPING
        );

        if (null !== $annotation) {
            $classMetadata->mappingTable = $annotation->getMappings();
        } else {
            $classMetadata->mappingTable = Mapping::getDefaultMapping();
        }

        $hasIdField = false;

        foreach ($class->getProperties() as $reflectionProperty) {
            $propertyMetadata = new PropertyMetadata($class->getName(), $reflectionProperty->getName());

            /** @var Id $annotation */
            $annotation = $this->reader->getPropertyAnnotation(
                $reflectionProperty,
                self::ANNOTATION_ID
            );

            if (null !== $annotation) {
                $classMetadata->id = $annotation->name;
                if (true === $hasIdField) {
                    $message = "Duplicate Id field declaration for class '%s' found.";
                    throw new \LogicException(sprintf($message, $classMetadata->reflection->getName()));
                }
                $hasIdField        = true;

                continue;
            }

            /** @var Field $annotation */
            $annotation = $this->reader->getPropertyAnnotation(
                $reflectionProperty,
                self::ANNOTATION_FIELD
            );

            if (null === $annotation) {
                continue;
            }

            $propertyMetadata->boost = $annotation->boost;
            $propertyMetadata->multi = $annotation->isMultiValuedType($annotation->type);
            $propertyMetadata->type  = $annotation->type;
            $propertyMetadata->propertyAccess = $annotation->propertyAccess;

            $mapping = array($annotation->type => $classMetadata->mappingTable[$annotation->type]);
            $name    = (null !== $annotation->name) ? $annotation->name : $reflectionProperty->getName();

            $propertyMetadata->fieldName = $annotation->getFieldName($mapping, $name);

            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        if (false === $hasIdField) {
            $message = "The class '%s' has a Solarium Document Declaration, but no Id field.";

            throw new \LogicException(sprintf($message, $classMetadata->reflection->getName()));
        }

        return $classMetadata;
    }

    /**
     * @param Document $documentAnnotation
     * @param ClassMetadata $classMetadata
     */
    private function setDocumentDataToClassMetadata(Document $documentAnnotation, ClassMetadata $classMetadata)
    {
        /** @var Operation $operation */
        foreach ($documentAnnotation->operations as $type => $operation) {
            $classMetadata->operations[$type] = $operation->service;
        }

        $classMetadata->boost = $documentAnnotation->boost;
    }
}
