TPSolariumExtensionsBundle
==========================

An extension for the [**NelmioSolariumBundle**][1] which provides an AnnotationDriver for
Document indexing configurations with support for multi-valued fields.

## Requirements

- Symfony >= 2.2.0 (Since the PropertyAccess Component is used)
- NelmioSolariumBundle >= 1.1.0

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

Add the NelmioSolariumBundle to your AppKernel.php:

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


## Configuration

### Prerequisites

First configure the NelmioSolariumBundle clients as described [**here**][2]:

### Configure metadata cache directory (if nessecary)

The ClassMetadata gets cached by default, and you can change the cache directory here.

TODO: Add configuration for different Cache engines.

```yaml
tp_solarium_extensions:
    metadata_cache_dir: %kernel.cache_dir%/solarium_extensions
```

### Example Annotation configuration

```php
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
     * Constructor
     */
    public function __construct()
    {
        $this->collection = new ArrayCollection();
        $this->dateCollection = new ArrayCollection();

        for ($i = 0; $i < 3; $i++) {
            $object = new \stdClass();
            $object->multiName = "test$i";

            $this->collection->add($object);

            $this->dateCollection->add(new \DateTime("201{$i}-04-24", new \DateTimeZone('UTC')));
        }

        $this->date = new \DateTime('2012-03-24', new \DateTimeZone('UTC'));
    }
}
```

[1]: https://github.com/nelmio/NelmioSolariumBundle
[2]: https://github.com/nelmio/NelmioSolariumBundle#basic-configuration
