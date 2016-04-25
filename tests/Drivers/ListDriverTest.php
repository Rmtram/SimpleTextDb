<?php

namespace Rmtram\SimpleTextDb\TestCase\Drivers;

use Rmtram\SimpleTextDb\Driver\ListDriver;
use Rmtram\SimpleTextDb\Query\Where;
use Rmtram\SimpleTextDb\Util\Unique;

/**
 * Class ListDriverTest
 * @package Rmtram\SimpleTextDb\TestCase\Drivers
 */
class ListDriverTest extends AbstractDriverTest
{
    /**
     * @covers Connector::connection
     * @covers ListDriver::add
     */
    public function testAdd()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $this->assertTrue($example->add(['id' => 1]));
        $this->assertFalse($example->add([1, 2, 3]));
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::eq
     * @covers Result::count
     */
    public function testBulkAdd()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $this->assertTrue($example->bulkAdd($this->mock(100)));
        $count = $example->find(function(Where $where) {
            $where->eq('name', 'example');
        })->count();
        $this->assertEquals(100, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers ListDriver::update
     * @covers Where::eq
     * @covers Result::get
     */
    public function testUpdate()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $example->update(['name' => 'example1'], function(Where $where) {
            $where->eq('name', 'example');
        });
        $items = $example->find(function(Where $where) {
            $where->eq('name', 'example');
        })->get();
        $bool = true;
        foreach ($items as $item) {
            if ($item['name'] !== 'example1') {
                $bool = false;
            }
        }
        $this->assertTrue($bool);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers ListDriver::delete
     * @covers Where::eq
     * @covers Result::exists
     */
    public function testDelete()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $example->delete(function(Where $where) {
            $where->eq('name', 'example');
        });
        $exists = $example->find(function(Where $where) {
            $where->eq('name', 'example');
        })->exists();
        $this->assertFalse($exists);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers ListDriver::truncate
     * @covers Where::eq
     * @covers Result::exists
     */
    public function testTruncate()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $example->truncate();
        $exists = $example->find(function(Where $where) {
            $where->eq('name', 'example');
        })->exists();
        $this->assertFalse($exists);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::eq
     * @covers Result::first
     */
    public function testFindFirst()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $item = $example->find(function(Where $where) {
            $where->eq('name', 'example');
        })->first();
        $this->assertEquals('example', $item['name']);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::eq
     * @covers Result::count
     */
    public function testWhereEqualWithMultiple()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->eq('name', 'example')->eq('age', 20);
        })->count();
        $this->assertEquals(0, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::eq
     * @covers Result::count
     */
    public function testWhereOrEqualWithMultiple()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->eq('name', 'none')->orEq('age', 18);
        })->count();
        $this->assertEquals(100, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::notEq
     * @covers Result::count
     */
    public function testWhereNotEqual()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->notEq('name', 'example');
        })->count();
        $this->assertEquals(0, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::notEq
     * @covers Where::eq
     * @covers Result::count
     */
    public function testWhereOrNotEqual()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->eq('age', 18)->orNotEq('name', 'example');
        })->count();
        $this->assertEquals(0, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::lt
     * @covers Result::count
     */
    public function testWhereLt()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->lt('age', 17);
        })->count();
        $this->assertEquals(100, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::orLt
     * @covers Where::eq
     * @covers Result::count
     */
    public function testWhereOrLt()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->eq('name', 'example')->orLt('age', 18);
        })->count();
        $this->assertEquals(0, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::lte
     * @covers Result::count
     */
    public function testWhereLte()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->lte('age', 18);
        })->count();
        $this->assertEquals(100, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::eq
     * @covers Where::orLte
     * @covers Result::count
     */
    public function testWhereOrLte()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->eq('name', 'example')->orLte('age', 18);
        })->count();
        $this->assertEquals(100, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::gt
     * @covers Result::count
     */
    public function testWhereGt()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->gt('age', 17);
        })->count();
        $this->assertEquals(0, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::orGt
     * @covers Where::eq
     * @covers Result::count
     */
    public function testWhereOrGt()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->eq('name', 'example')->orGt('age', 18);
        })->count();
        $this->assertEquals(0, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::gte
     * @covers Result::count
     */
    public function testWhereGte()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->gte('age', 18);
        })->count();
        $this->assertEquals(100, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::eq
     * @covers Where::orGte
     * @covers Result::count
     */
    public function testWhereOrGte()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->eq('name', 'example')->orGte('age', 18);
        })->count();
        $this->assertEquals(100, $count);
        $this->reset();
    }

    /**
 * @covers Connector::connection
 * @covers ListDriver::bulkAdd
 * @covers ListDriver::find
 * @covers Where::like
 * @covers Result::count
 */
    public function testWhereLikeWithForward()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->like('name', '%exa');
        })->count();
        $this->assertEquals(100, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::like
     * @covers Result::count
     */
    public function testWhereLikeWithBackward()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->like('name', 'ple%');
        })->count();
        $this->assertEquals(100, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::like
     * @covers Result::count
     */
    public function testWhereLikeWithForwardAndBackward()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->like('name', '%exa%');
        })->count();
        $this->assertEquals(100, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::orLike
     * @covers Where::eq
     * @covers Result::count
     */
    public function testWhereOrLikeWithForward()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->eq('age', 20)->orLike('name', '%exa');
        })->count();
        $this->assertEquals(100, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::orLike
     * @covers Where::eq
     * @covers Result::count
     */
    public function testWhereOrLikeWithBackward()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->eq('age', 20)->orLike('name', 'ple%');
        })->count();
        $this->assertEquals(100, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::orLike
     * @covers Where::eq
     * @covers Result::count
     */
    public function testWhereOrLikeWithForwardAndBackward()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->eq('age', 20)->orLike('name', '%exa%');
        })->count();
        $this->assertEquals(100, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::notLike
     * @covers Result::count
     */
    public function testWhereNotLikeWithForward()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->notLike('name', '%exa');
        })->count();
        $this->assertEquals(0, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::notLike
     * @covers Result::count
     */
    public function testWhereNotLikeWithBackward()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->notLike('name', 'ple%');
        })->count();
        $this->assertEquals(0, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::notLike
     * @covers Result::count
     */
    public function testWhereNotLikeWithForwardAndBackward()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->notLike('name', '%exa%');
        })->count();
        $this->assertEquals(0, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::orNotLike
     * @covers Where::eq
     * @covers Result::count
     */
    public function testWhereOrNotLikeWithForward()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->eq('age', 18)->orNotLike('name', '%exa');
        })->count();
        $this->assertEquals(0, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::orNotLike
     * @covers Where::eq
     * @covers Result::count
     */
    public function testWhereOrNotLikeWithBackward()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->notLike('name', 'ple%');
        })->count();
        $this->assertEquals(0, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::find
     * @covers Where::orNotLike
     * @covers Where::eq
     * @covers Result::count
     */
    public function testWhereOrNotLikeWithForwardAndBackward()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $count = $example->find(function(Where $where) {
            $where->eq('age', 18)->orNotLike('name', '%exa%');
        })->count();
        $this->assertEquals(0, $count);
        $this->reset();
    }

    /**
     * @covers Connector::connection
     * @covers ListDriver::bulkAdd
     * @covers ListDriver::all
     */
    public function testAll()
    {
        /** @var ListDriver $example */
        $example = $this->connector()->connection('example', 'list');
        $example->bulkAdd($this->mock());
        $this->assertEquals(100, count($example->all()));
        $this->reset();
    }

    /**
     * @param int $count
     * @return array
     */
    private function mock($count = 100)
    {
        $items = [];
        for($i = 0; $i < $count; $i++) {
            $items[] = [
                'id'   => Unique::id(),
                'name' => 'example',
                'age'  => 18
            ];
        }
        return $items;
    }
}