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
 *          @Solarium\Operation("save", service="solarium.client.save", endpoint="test.endpoint"),
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
     * @Solarium\Id("custom_id", propertyAccess="id")
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
     * @var bool
     *
     * @Solarium\Field(type="float")
     */
    public $floatString = "24.35";

    /**
     * @var bool
     *
     * @Solarium\Field(type="float")
     */
    public $floatValue = 22.55;

    /**
     * @var bool
     *
     * @Solarium\Field(type="integer")
     */
    public $intString = "25";

    /**
     * @var bool
     *
     * @Solarium\Field(type="integer")
     */
    public $intValue = 26;

    /**
     * @var ArrayCollection
     *
     * @Solarium\Field(type="text_multi", propertyAccess="multiName")
     */
    public $collection;

    /**
     * @var \DateTime
     *
     * @Solarium\Field(type="date")
     */
    public $date;

    /**
     * @var ArrayCollection
     *
     * @Solarium\Field(type="date_multi", propertyAccess="__raw__")
     */
    public $dateCollection;

    /**
     * @var \stdClass
     *
     * @Solarium\Field(type="string", propertyAccess="title", inflect=false, useMapping=false)
     */
    public $objectWithPropertyAccess;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->collection = new ArrayCollection();
        $this->dateCollection = new ArrayCollection();
        $this->objectWithPropertyAccess = new \stdClass();
        $this->objectWithPropertyAccess->title = 'objectWithPropertyAccess';

        for ($i = 0; $i < 3; $i++) {
            $object = new \stdClass();
            $object->multiName = "test$i";

            $this->collection->add($object);

            $this->dateCollection->add(new \DateTime("201{$i}-04-24", new \DateTimeZone('UTC')));
        }

        $this->date = new \DateTime('2012-03-24', new \DateTimeZone('UTC'));
    }
}
