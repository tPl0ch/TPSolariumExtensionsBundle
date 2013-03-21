<?php
/**
 * SolariumDriver.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Doctrine\Annotations;

use Doctrine\Common\Annotations\AnnotationReader;
use TP\SolariumExtensionsBundle\Manager\SolariumServiceManager;

/**
 * Class SolariumDriver
 *
 * @package TP\SolariumExtensionsBundle\Doctrine\Annotations
 */
class SolariumDriver
{
    const ANNOTATION_DOCUMENT  = 'TP\SolariumExtensionsBundle\Doctrine\Annotations\Document';
    const ANNOTATION_OPERATION = 'TP\SolariumExtensionsBundle\Doctrine\Annotations\Operation';
    const ANNOTATION_FIELD     = 'TP\SolariumExtensionsBundle\Doctrine\Annotations\Field';
    const ANNOTATION_MAPPING   = 'TP\SolariumExtensionsBundle\Doctrine\Annotations\Mapping';
    const ANNOTATION_ID        = 'TP\SolariumExtensionsBundle\Doctrine\Annotations\Id';

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
}
