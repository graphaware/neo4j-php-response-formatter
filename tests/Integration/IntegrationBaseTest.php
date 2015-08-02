<?php

namespace GraphAware\NeoClient\Formatter\Tests\Integration;

use GraphAware\Neo4j\GraphUnit\Neo4jGraphDatabaseTestCase;
use Neoxygen\NeoClient\Client;
use Neoxygen\NeoClient\ClientBuilder;

abstract class IntegrationBaseTest extends Neo4jGraphDatabaseTestCase
{
    /**
     * @var \Neoxygen\NeoClient\Client
     */
    protected $conn;

    public function getConnection()
    {
        if (!$this->conn instanceof Client) {
            $this->conn = ClientBuilder::create()
              ->addConnection('default', 'http', 'localhost', 7474, true, 'neo4j', 'veryCoolMax')
              ->setAutoFormatResponse(true)
              ->enableNewFormattingService()
              ->build();
        }

        return $this->conn;
    }
}