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

use Doctrine\ORM\Event\LifecycleEventArgs;

use Symfony\Component\HttpKernel\Event\PostResponseEvent;

use TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation;
use TP\SolariumExtensionsBundle\Processor\Processor;

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

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();

        if ($this->processor->needsProcessing($object, Operation::OPERATION_SAVE)) {
            $this->processor->process($object, Operation::OPERATION_SAVE);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();

        if ($this->processor->needsProcessing($object, Operation::OPERATION_UPDATE)) {
            $this->processor->process($object, Operation::OPERATION_UPDATE);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();

        if ($this->processor->needsProcessing($object, Operation::OPERATION_DELETE)) {
            $this->processor->process($object, Operation::OPERATION_DELETE);
        }
    }

    /**
     * @param PostResponseEvent $event
     */
    public function onKernelTerminate(PostResponseEvent $event)
    {
        $this->getProcessor()->flush();
    }
}
