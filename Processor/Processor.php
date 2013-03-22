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
     * @param MetadataFactoryInterface $metadataFactory
     * @param SolariumServiceManager $serviceManager
     */
    public function __construct(MetadataFactoryInterface $metadataFactory, SolariumServiceManager $serviceManager)
    {
        $this->metadataFactory = $metadataFactory;
        $this->serviceManager  = $serviceManager;
    }
}
