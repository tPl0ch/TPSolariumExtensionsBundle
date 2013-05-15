<?php
/**
 * DoctrineListenerTest.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Tests\Manager;

use TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation;
use TP\SolariumExtensionsBundle\EventListener\DoctrineListener;

/**
 * Class DoctrineListenerTest
 *
 * @package TP\SolariumExtensionsBundle\Tests\Manager
 */
class DoctrineListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DoctrineListener
     */
    public $listener;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    public $processor;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->processor = $this->getMockBuilder('TP\SolariumExtensionsBundle\Processor\Processor')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->listener = new DoctrineListener($this->processor);
    }

    /**
     * @dataProvider getProcessingData
     */
    public function testProcessing($method, $operation, $return, $hasOperation)
    {
        $event = $this->getMockBuilder('Doctrine\ORM\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $event->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue($return))
        ;

        $this->processor
            ->expects($this->once())
            ->method('needsProcessing')
            ->with($return, $operation)
            ->will($this->returnValue($hasOperation))
        ;

        if ($hasOperation) {
            $this->processor
                ->expects($this->once())
                ->method('process')
                ->with($return, $operation)
            ;
        } else {
            $this->processor
                ->expects($this->never())
                ->method('process')
            ;
        }


        $this->listener->{$method}($event);
    }

    public function testOnKernelTerminate()
    {
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\PostResponseEvent')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->processor
            ->expects($this->once())
            ->method('flush')
        ;

        $this->listener->onKernelTerminate($event);
    }

    public function getProcessingData()
    {
        return array(
            array('postPersist', Operation::OPERATION_SAVE, 'test.save', true),
            array('postUpdate', Operation::OPERATION_UPDATE, 'test.update', true),
            array('preRemove', Operation::OPERATION_DELETE, 'test.delete', true),
            array('postPersist', Operation::OPERATION_SAVE, 'test.save', false),
            array('postUpdate', Operation::OPERATION_UPDATE, 'test.update', false),
            array('preRemove', Operation::OPERATION_DELETE, 'test.delete', false)
        );
    }
}
