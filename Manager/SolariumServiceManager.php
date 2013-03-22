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
use TP\SolariumExtensionsBundle\Metadata\ClassMetadata;

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
     * @var array
     */
    private $updateStack = array();

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

    /**
     * @param ClassMetadata $classMetadata
     * @param $operation
     *
     * @return \Solarium\QueryType\Update\Query\Query
     */
    public function getUpdateQuery(ClassMetadata $classMetadata, $operation)
    {
        if (!$classMetadata->hasOperation($operation)) {
            return null;
        }

        $service = $classMetadata->getServiceId($operation);

        if (array_key_exists($service, $this->updateStack)) {
            return $this->updateStack[$service];
        }

        $this->updateStack[$service] = $this
            ->getClient($service)
            ->createUpdate()
        ;

        return $this->updateStack[$service];
    }

    /**
     * Send all outstanding update requests to the various clients.
     *
     * @return void
     */
    public function doUpdate()
    {
        foreach ($this->updateStack as $service => $update) {
            $client = $this->getClient($service);
            $client->update($update);
        }
    }
}
