<?php
/**
 * AnnotationStub1.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Tests\Classes;

use Doctrine\Common\Collections\ArrayCollection;
use TP\SolariumExtensionsBundle\Doctrine\Annotations as Solarium;

/**
 * Class AnnotationStub1
 *
 * @package TP\SolariumExtensionsBundle\Tests\Classes
 *
 * @Solarium\Document(
 *      operations={
 *          @Solarium\Operation("save", service="solarium.client.save"),
 *          @Solarium\Operation("update", service="solarium.client.update")
 *      },
 *      boost="2.4"
 * )
 * @Solarium\Mapping(
 *      mapping={"text_multi"="_tmulti"},
 *      strict=true
 * )
 */
class AnnotationStub1
{
    /**
     * @var int
     *
     * @Solarium\Id("custom_id")
     */
    public $id = 1423;

    /**
     * @var string
     *
     * @Solarium\Field()
     */
    public $string = 'string';

    /**
     * @var string
     *
     * @Solarium\Field(boost="2.3")
     */
    public $boostedField = 'boosted_string';

    /**
     * @var string
     *
     * @Solarium\Field(useMapping=false)
     */
    public $inflectedNoMapping = 'inflectedNoMapping';

    /**
     * @var string
     *
     * @Solarium\Field(inflect=false)
     */
    public $mappedNoInflection = 'mappedNoInflection';

    /**
     * @var string
     *
     * @Solarium\Field(inflect=false, useMapping=false)
     */
    public $noMappingNoInflection = 'noMappingNoInflection';

    /**
     * @var string
     *
     * @Solarium\Field(name="myCustomName")
     */
    public $customName = 'customName';

    /**
     * @var bool
     *
     * @Solarium\Field(type="boolean")
     */
    public $bool = false;

    /**
     * @var string
     *
     * @Solarium\Field(type="text_multi", propertyAccess="multiName")
     */
    public $collection;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->collection = new ArrayCollection();

        for ($i = 0; $i < 3; $i++) {
            $object = new \stdClass();
            $object->multiName = "test$i";

            $this->collection->add($object);
        }
    }
}
