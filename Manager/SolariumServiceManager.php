<?php
/**
 * SolariumServiceManager.php
 *
 * This file is part of the SolariumExtensionsBundle.
 * Read the LICENSE file in the root of the project for information on copyright.
 *
 * @author Thomas Ploch <tp@responsive-code.de>
 * @since  19.03.13
 */
namespace TP\SolariumExtensionsBundle\Manager;

use Solarium\Client;

/**
 * Class SolariumServiceManager
 *
 * @package TP\SolariumExtensionsBundle\Manager
 */
class SolariumServiceManager
{
    /**
     * @var array
     */
    private $clients = array();

    /**
     * @param Client $client
     * @param string $id
     *
     * @return SolariumServiceManager
     */
    public function setClient(Client $client, $id)
    {
        $this->clients[$id] = $client;

        return $this;
    }

    /**
     * @param string $id
     *
     * @return Client
     *
     * @throws \InvalidArgumentException
     */
    public function getClient($id)
    {
        if (!array_key_exists($id, $this->clients)) {
            throw new \InvalidArgumentException(sprintf("Solarium service with id '%s' not found.", $id));
        }

        return $this->clients[$id];
    }
}
