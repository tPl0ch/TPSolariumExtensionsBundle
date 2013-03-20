<?php
/**
 * TPSolariumExtensionsBundle.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use TP\SolariumExtensionsBundle\DependencyInjection\Compiler\NelmioConfigSnifferPass;

/**
 * Class TPSolariumExtensionsBundle
 *
 * @package TP\SolariumExtensionsBundle
 */
class TPSolariumExtensionsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new NelmioConfigSnifferPass(), PassConfig::TYPE_OPTIMIZE);
    }
}
