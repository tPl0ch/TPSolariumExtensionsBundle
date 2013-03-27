TPSolariumExtensionsBundle
==========================

An extension for the [**NelmioSolariumBundle**][1] which provides an AnnotationDriver for
Document indexing configurations with support for multi-valued fields.

**WARNING - This Bundle is still in heavy development and, although it is working, it is
far from finished. I urge you to report any bugs you might come across (and you probably
will) in the issues section. Thanks in advance for your help!**

[![Build Status](https://travis-ci.org/tPl0ch/TPSolariumExtensionsBundle.png?branch=master)](https://travis-ci.org/tPl0ch/TPSolariumExtensionsBundle)

## Requirements

- Symfony >= 2.2.0 (Since the PropertyAccess Component is used)
- NelmioSolariumBundle >= 2.0.0
- solarium/solarium >= 3.0.0
- jms/metadata dev-master
- doctrine/annotations dev-master
- doctrine/inflector dev-master

## Installation

Add TPSolariumExtensionsBundle in your ```composer.json```:

```json
{
    "require": {
        "tp/solarium-extensions-bundle": "dev-master"
    }
}
```

Download Bundle:

```bash
php composer.phar update tp/solarium-extensions-bundle
```

Add the TPSolariumExtensionsBundle to your AppKernel.php:

```php
public function registerBundles()
{
    $bundles = array(
        ...
        new TP\SolariumExtensionsBundle\TPSolariumExtensionsBundle(),
        ...
    );
    ...
}
```

**This is important to make this Bundle work correctly**

Make your ```AppKernel``` class implement ```Symfony\Component\HttpKernel\TerminableInterface```:

```php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\TerminableInterface;

class AppKernel extends Kernel implements TerminableInterface
{
    ...
}
```

## Configuration

### Prerequisites

First configure the NelmioSolariumBundle clients as described [**here**][2]:

### Configure metadata cache directory (if nessecary)

The ClassMetadata gets cached by default, and you can change the cache directory here.

TODO: Add configuration for different Cache engines.

```yaml
tp_solarium_extensions:
    metadata_cache_dir: %kernel.cache_dir%/%kernel.environment%/solarium_extensions
```

## Example Annotation configuration

Check the comments on this class to get a fist hang of the configuration until I have written a proper documentation.
The test suite is also a good point to check what's possible.

```php
/**
 * This is the main Document Annotation. The Nelmio service id is mandatory in this case:
 *
 * @Solarium\Document("solarium.client.default")
 *
 * This is the same as:
 *
 * @Solarium\Document(
 *      operations={
 *          @Solarium\Operation("all", service="solarium.client.default")
 *      }
 * )
 *
 * Both these notations will listen to all 'save', 'update', and 'delete' transactions via postPersist,
 * postUpdate and postDelete. Commits will only be done when the kernel terminates, so that expensive
 * requests can be done when the Response is already sent to the Client.
 *
 * You can also assign different NelmioSolariumbundle clients to different operations. In the next
 * example, only 'save' and 'update' will be processed, with the coresponding Clients.
 *
 * @Solarium\Document(
 *      operations={
 *          @Solarium\Operation("save", service="solarium.client.save"),
 *          @Solarium\Operation("update", service="solarium.client.update")
 *      }
 * )
 *
 * But that's not all, you can even specify different endpoints for the same client!
 * In the following example, the "save" opration will use the "anotherOne" endpoint, while the
 * "update" operation uses the default endpoint for the given client service.
 *
 * @Solarium\Document(
 *      operations={
 *          @Solarium\Operation("save", service="solarium.client.save", endpoint="anotherOne"),
 *          @Solarium\Operation("update", service="solarium.client.update")
 *      }
 * )

 * You want to add a document boost? No problem:
 *
 * @Solarium\Document("solarium.client.default", boost="2.4")
 *
 * This is an example of the Mapping Annotation, which you can map field types to Solr's dynamic field
 * suffixes.
 * The strict parameter is for strict checking of field types. If no mapping is specified, a default
 * mapping taken from the current Solr default schema.xml file.
 *
 * @Solarium\Mapping(
 *      mapping={"text_multi"="_tmulti"},
 *      strict=false
 * )
 */
class Example
{
    /**
     * @var int
     *
     * This is the Id Annotation, which is **REQUIRED** on every document.
     * The value "custom_id" is the ID field in solr (if you omit the value, it defaults to 'id'), and
     * propertyAccess is the propertyPath for the new PropertyAccess component.
     *
     * @Solarium\Id("custom_id", propertyAccess="id")
     */
    public $id = 1423;

    /**
     * @var string
     *
     * Fields have as standard type Field::TYPE_STRING = 'string'
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
     * Use the 'useMapping' parameter to control if you want the dynamic field suffix to be automatically
     * appended, which is the default behavior.
     *
     * @Solarium\Field(useMapping=true)
     */
    public $inflectedNoMapping = 'inflectedNoMapping';

    /**
     * @var string
     *
     * for BC with older Solr versions, inflecting the field names is recommended to work with older
     * filters and components.
     *
     * @Solarium\Field(inflect=true)
     */
    public $mappedNoInflection = 'mappedNoInflection';

    /**
     * @var string
     *
     * Use the 'name' parameter to generate a custom field name instead of the property name.
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
     * @var ArrayCollection
     *
     * The multi-valued field types need to be a Traversable, so either an array or sth that
     * implements \Traversable.
     * The propertyAccess parameter is **MANDATORY** for multi-valued field types, so that the
     * PropertyAccess component can fetch the values from the collection objects.
     *
     * @Solarium\Field(type="text_multi", propertyAccess="multiName")
     */
    public $collection;

    /**
     * @var array
     *
     * The special "__raw__" value for propertyAccess skips the value fetching and just takes the
     * raw items from the collection, like array('value1', 'value2', 'value3').
     *
     * @Solarium\Field(type="string_multi", propertyAccess="__raw__")
     */
    public $stringCollection;

    /**
     * @var object
     *
     * The propertyAccess parameter can also be used to extract a single value from a single
     * object. In this case imagine this object:
     *
     * $this->singleObject = new MySpecialObject();
     * $this->singleObject->title = "Hello propertyAccess on single object";
     *
     * The resulting string in the solr data will be "Hello propertyAccess on single object"!
     * And the PropertyAccess component is very good in guessing the access method, so you
     * don't have to worry if it's a getter, public var, or sth else.
     *
     * @Solarium\Field(type="string", propertyAccess="title")
     */
    public $singleObject;

    /**
     * @var \DateTime
     *
     * Date fields will be automatically converted from \DateTime to UTC Solr Time strings
     *
     * @Solarium\Field(type="date")
     */
    public $date;
}
```

## Currently implemented Field types:

```php
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
```


TODO: Make extensive documentation available.

[1]: https://github.com/nelmio/NelmioSolariumBundle
[2]: https://github.com/nelmio/NelmioSolariumBundle#basic-configuration

## Run the testsuite

```bash
$ phpunit -c phpunit.xml.dist
```
