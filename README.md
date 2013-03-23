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

```sh
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

```yaml
tp_solarium_extensions:
    metadata_cache_dir: %kernel.cache_dir%/solarium_extensions
```


[1]: https://github.com/nelmio/NelmioSolariumBundle
[2]: https://github.com/nelmio/NelmioSolariumBundle#basic-configuration
