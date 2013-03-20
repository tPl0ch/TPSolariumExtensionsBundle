<?php
/**
 * Field.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Doctrine\Annotations;

use TP\SolariumExtensionsBundle\Doctrine\Annotations\Annotation;
use Doctrine\Common\Inflector\Inflector;

/**
 * Class Field
 *
 * @package TP\SolariumExtensionsBundle\Doctrine\Annotations
 * @Annotation
 */
class Field extends Annotation
{
    /**
     * Default field constants
     *
     * @var string
     */
    const TYPE_INT           = 'integer';
    const TYPE_INT_MULTI     = 'integer_multi';
    const TYPE_STRING        = 'string';
    const TYPE_STRING_MULTI  = 'string_multi';
    const TYPE_LONG          = 'long';
    const TYPE_LONG_MULTI    = 'long_multi';
    const TYPE_TEXT          = 'text';
    const TYPE_TEXT_MULTI    = 'text_multi';
    const TYPE_BOOLEAN       = 'boolean';
    const TYPE_BOOLEAN_MULTI = 'boolean_multi';
    const TYPE_FLOAT         = 'float';
    const TYPE_FLOAT_MULTI   = 'float_multi';
    const TYPE_DOUBLE        = 'double';
    const TYPE_DOUBLE_MULTI  = 'double_multi';
    const TYPE_DATE          = 'date';
    const TYPE_DATE_MULTI    = 'date_multi';
    const TYPE_LOCATION      = 'location';

    /**
     * Holds the field types that are multiValued
     *
     * @var array
     */
    private $multiFields = array(
        self::TYPE_INT_MULTI,
        self::TYPE_STRING_MULTI,
        self::TYPE_LONG_MULTI,
        self::TYPE_TEXT_MULTI,
        self::TYPE_BOOLEAN_MULTI,
        self::TYPE_FLOAT_MULTI,
        self::TYPE_DOUBLE_MULTI,
        self::TYPE_DATE_MULTI,
    );

    /**
     * @return array
     */
    public static function getFieldTypes()
    {
        return array(
            self::TYPE_INT           ,
            self::TYPE_INT_MULTI     ,
            self::TYPE_STRING        ,
            self::TYPE_STRING_MULTI  ,
            self::TYPE_LONG          ,
            self::TYPE_LONG_MULTI    ,
            self::TYPE_TEXT          ,
            self::TYPE_TEXT_MULTI    ,
            self::TYPE_BOOLEAN       ,
            self::TYPE_BOOLEAN_MULTI ,
            self::TYPE_FLOAT         ,
            self::TYPE_FLOAT_MULTI   ,
            self::TYPE_DOUBLE        ,
            self::TYPE_DOUBLE_MULTI  ,
            self::TYPE_DATE          ,
            self::TYPE_DATE_MULTI    ,
            self::TYPE_LOCATION      ,
        );
    }

    /**
     * @return array
     */
    public static function getMultiFieldTypes()
    {
        return array(
            self::TYPE_INT_MULTI,
            self::TYPE_STRING_MULTI,
            self::TYPE_LONG_MULTI,
            self::TYPE_TEXT_MULTI,
            self::TYPE_BOOLEAN_MULTI,
            self::TYPE_FLOAT_MULTI,
            self::TYPE_DOUBLE_MULTI,
            self::TYPE_DATE_MULTI,
        );
    }

    /**
     * The field name to use, If this is not set, will use the class variable name.
     *
     * @var string
     */
    public $name;

    /**
     * The default Field type.
     * Defaults to 'string'
     *
     * @var string
     */
    public $type = self::TYPE_STRING;

    /**
     * The field boost that should be set to this field.
     *
     * @var int
     */
    public $boost = 0;

    /**
     * Indicates if the MappingTable should be used to generate the field name.
     * Defaults to TRUE
     *
     * @var Boolean
     */
    public $useMapping = true;

    /**
     * Indicates if field names should be inflected into underscore names.
     * Solr recommends this for BC with older Solr components.
     *
     * @var Boolean
     */
    public $inflect = true;

    /**
     * Creates a given field name with given settings.
     *
     * @param array $mapping
     *
     * @return string
     */
    public function getFieldName(Array &$mapping)
    {
        $name = $this->name;

        if ($this->inflect) {
            $name = Inflector::tableize($name);
        }

        if ($this->useMapping) {
            $name .= $mapping[$this->type];
        }

        return $name;
    }
}
