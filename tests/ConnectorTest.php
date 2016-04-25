<?php

namespace Rmtram\SimpleTextDb\TestCase;

use Rmtram\SimpleTextDb\Connector;

/**
 * Class ConnectorTest
 * @package Rmtram\SimpleTextDb\TestCase
 */
class ConnectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Connector
     */
    private $connector;

    public function setUp()
    {
        $this->connector = new Connector(__DIR__ . '/fixtures');
    }

    public function testConnectionWithListDriver()
    {
        $example = $this->connector->connection('example1', 'list');
        $this->assertInstanceOf('Rmtram\SimpleTextDb\Driver\ListDriver', $example);
    }

    public function testConnectionWithHashMapDriver()
    {
        $example = $this->connector->connection('example2', 'hash');
        $this->assertInstanceOf('Rmtram\SimpleTextDb\Driver\HashMapDriver', $example);
    }


    public function testDrop()
    {
        $this->assertTrue($this->connector->drop('example1'));
        $this->assertTrue($this->connector->drop('example2'));
    }

}