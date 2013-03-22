<?php
/**
 * DoctrineListener.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation;
use TP\SolariumExtensionsBundle\Processor\Processor;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class DoctrineListener
 *
 * @package TP\SolariumExtensionsBundle\EventSubscriber
 */
class DoctrineListener
{
    /**
     * @var \TP\SolariumExtensionsBundle\Processor\Processor
     */
    private $processor;

    /**
     * @param Processor $processor
     */
    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * @return \TP\SolariumExtensionsBundle\Processor\Processor
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        $classMetadata = $this->processor->getClassMetadata($object);

        if (null === $classMetadata) {
            return null;
        }

        if (array_key_exists(Operation::OPERATION_ALL, $classMetadata->operations)) {
            $service = $classMetadata->operations[Operation::OPERATION_ALL];
        } elseif (array_key_exists(Operation::OPERATION_SAVE, $classMetadata->operations)) {
            $service = $classMetadata->operations[Operation::OPERATION_SAVE];
        } else {
            return null;
        }

        $accessor = PropertyAccess::getPropertyAccessor();

        $client = $this->processor
            ->getServiceManager()
            ->getClient($service)
        ;
        $update = $client->createUpdate();
        /** @var \Solarium\QueryType\Update\Query\Document\Document $document */
        $document = $update->createDocument();
        $document->setBoost($classMetadata->boost);
        $document->addField($classMetadata->id, $object->getId());

        /** @var \TP\SolariumExtensionsBundle\Metadata\PropertyMetadata $property */
        foreach ($classMetadata->propertyMetadata as $property) {
            if (true === $property->multi) {
                foreach ($property->getValue($object) as $item) {
                    $document->addField($property->fieldName, $accessor->getValue($item, $property->propertyAccess));
                }
            } else {
                $document->addField($property->fieldName, $property->getValue($object));
            }

            $document->setFieldBoost($property->fieldName, $property->boost);
        }

        $update->addDocument($document);
        $update->addCommit();

        $client->update($update);
    }
}
