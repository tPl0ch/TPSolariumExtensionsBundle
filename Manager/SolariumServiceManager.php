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
use Solarium\Core\Query\Query;
use Solarium\Core\Query\QueryInterface;
use TP\SolariumExtensionsBundle\Metadata\ClassMetadata;

/**
 * Class SolariumServiceManager
 *
 * @package TP\SolariumExtensionsBundle\Manager
 */
class SolariumServiceManager
{
    const DEFAULT_ENDPOINT_KEY = '__default__';

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

        $service  = $classMetadata->getServiceId($operation);
        $endpoint = $classMetadata->getEndpoint($operation);

        if (null === $endpoint) {
            $endpoint = self::DEFAULT_ENDPOINT_KEY;
        }

        if (array_key_exists($service, $this->updateStack)) {
            return $this->updateStack[$service][$endpoint];
        }

        $this->updateStack[$service] = array();
        $this->updateStack[$service][$endpoint] = $this
            ->getClient($service)
            ->createUpdate()
        ;

        return $this->updateStack[$service][$endpoint];
    }

    /**
     * Send all outstanding update requests to the various clients.
     *
     * @return void
     */
    public function doUpdate()
    {
        foreach ($this->updateStack as $service => $config) {
            $client = $this->getClient($service);

            foreach ($config as $endpoint => $update) {
                if ($endpoint === self::DEFAULT_ENDPOINT_KEY) {
                    $endpoint = null;
                }

                if (!$update instanceof QueryInterface) {
                    continue;
                }

                $update->addCommit();
                $client->update($update, $endpoint);
            }
        }

        $this->updateStack = array();
    }
}
