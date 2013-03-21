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
use TP\SolariumExtensionsBundle\Doctrine\Annotations\SolariumDriver;

/**
 * Class DoctrineListener
 *
 * @package TP\SolariumExtensionsBundle\EventSubscriber
 */
class DoctrineListener
{
    /**
     * @var \TP\SolariumExtensionsBundle\Doctrine\Annotations\SolariumDriver
     */
    private $driver;

    /**
     * @param SolariumDriver $driver
     */
    public function __construct(SolariumDriver $driver)
    {
        $this->driver = $driver;
    }
}
