<?php

namespace Rmtram\SimpleTextDb\TestCase\Drivers;

use Rmtram\SimpleTextDb\Driver\HashMapDriver;

/**
 * Class HashMapDriverTest
 * @package Rmtram\SimpleTextDb\TestCase\Drivers
 */
class HashMapDriverTest extends AbstractDriverTest
{
    /**
     * @covers Connector::connection
     * @covers HashMapDriver::set
     */
    public function testSet()
    {
        /** @var HashMapDriver $example */
        $example = $this->connector()->connection('example', 'hash');
        $bool = $example->set('unique_1', ['name' => 'example']);
        $this->assertTrue($bool);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers HashMapDriver::set
     * @covers HashMapDriver::get
     */
    public function testGet()
    {
        /** @var HashMapDriver $example */
        $example = $this->connector()->connection('example', 'hash');
        $example->set('unique_1', ['name' => 'example']);
        $item = $example->get('unique_1');
        $this->assertEquals(['name' => 'example'], $item);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers HashMapDriver::set
     * @covers HashMapDriver::getAll
     */
    public function testAll()
    {
        /** @var HashMapDriver $example */
        $example = $this->connector()->connection('example', 'hash');
        $example->set('unique_1', ['name' => 'example']);
        $example->set('unique_2', ['name' => 'example']);
        $items = $example->all();
        $expect = [
            'unique_1' => ['name' => 'example'],
            'unique_2' => ['name' => 'example']
        ];
        $this->assertEquals($expect, $items);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers HashMapDriver::set
     * @covers HashMapDriver::has
     */
    public function testHas()
    {
        /** @var HashMapDriver $example */
        $example = $this->connector()->connection('example', 'hash');
        $example->set('unique_1', ['name' => 'example']);
        $this->assertTrue($example->has('unique_1'));
        $this->assertFalse($example->has('dummy'));
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers HashMapDriver::set
     * @covers HashMapDriver::delete
     * @covers HashMapDriver::get
     */
    public function testDelete()
    {
        /** @var HashMapDriver $example */
        $example = $this->connector()->connection('example', 'hash');
        $example->set('unique_1', ['name' => 'example']);
        $this->assertTrue($example->delete('unique_1'));
        $this->assertEmpty($example->get('unique_1'));
        $example->set('unique_1', ['name' => 'example']);
        $example->set('unique_2', ['name' => 'example']);
        $example->set('unique_3', ['name' => 'example']);
        $this->assertTrue($example->delete(['unique_1', 'unique_2', 'unique_3']));
        $this->assertEmpty($example->get('unique_1'));
        $this->assertEmpty($example->get('unique_2'));
        $this->assertEmpty($example->get('unique_3'));
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers HashMapDriver::set
     * @covers HashMapDriver::has
     */
    public function testTruncate()
    {
        /** @var HashMapDriver $example */
        $example = $this->connector()->connection('example', 'hash');
        $example->set('unique_1', ['name' => 'example']);
        $example->set('unique_2', ['name' => 'example']);
        $example->set('unique_3', ['name' => 'example']);
        $this->assertTrue(($example->truncate()));
        $this->assertFalse($example->has('unique_1'));
        $this->assertFalse($example->has('unique_2'));
        $this->assertFalse($example->has('unique_3'));
        $this->reset();
    }

}