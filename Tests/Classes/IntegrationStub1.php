<?php
/**
 * IntegrationStub1.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Tests\Classes;

use TP\SolariumExtensionsBundle\Doctrine\Annotations as Solarium;

/**
 * Class AnnotationStub1
 *
 * @package TP\SolariumExtensionsBundle\Tests\Classes
 *
 * @Solarium\Document(
 *      operations={
 *          @Solarium\Operation("save", service="solarium.client.client1", endpoint="default"),
 *          @Solarium\Operation("update", service="solarium.client.client1", endpoint="default")
 *      },
 *      boost="2.4"
 * )
 * @Solarium\Mapping(
 *      mapping={"text_multi"="_tmulti"},
 *      strict=true
 * )
 */
class IntegrationStub1
{
    /**
     * @var int
     *
     * @Solarium\Id("id", propertyAccess="id")
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
     * @Solarium\Field(type="text", boost="2.3")
     */
    public $boostedField = 'boosted string';

    /**
     * @var string
     *
     * @Solarium\Field(name="myCustomName")
     */
    public $customName = 'custom name';

    /**
     * @var bool
     *
     * @Solarium\Field(type="boolean")
     */
    public $bool = false;
}
