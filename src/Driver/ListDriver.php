<?php

namespace Rmtram\SimpleTextDb\Driver;

use Rmtram\SimpleTextDb\Config\Attribute;
use Rmtram\SimpleTextDb\Exceptions\FailureDeletedException;
use Rmtram\SimpleTextDb\Exceptions\InvalidArgumentException;
use Rmtram\SimpleTextDb\Query\Result;
use Rmtram\SimpleTextDb\Query\Where;

/**
 * Class ListDriver
 * @package Rmtram\SimpleTextDb\Driver
 */
class ListDriver extends AbstractDriver
{

    /**
     * @param Attribute $attribute
     */
    public function __construct(Attribute $attribute)
    {
        parent::__construct($attribute);
    }

    /***
     * @param array $value
     * @return bool
     */
    public function add(array $value)
    {
        if (array_keys($value) !== range(0, count($value) - 1)) {
            $this->memory->add($value);
            return true;
        }
        return false;
    }

    /**
     * @param array $values
     * @return bool
     */
    public function bulkAdd(array $values)
    {
        foreach ($values as $value) {
            if (!is_array($value)) {
                return false;
            }
            if (array_keys($value) === range(0, count($value) - 1)) {
                return false;
            }
        }
        return $this->memory->bulkAdd($values);
    }

    /**
     * @return array|null
     */
    public function all()
    {
        return $this->memory->get();
    }

    /**
     * @param callable $callable
     * @return Result
     */
    public function find(callable $callable)
    {
        $where = new Where($this->memory);
        $callable($where);
        return new Result($where, $this->memory);
    }

    /**
     * callable null is all update.
     * @param array $value
     * @param callable|null $callable
     * @return bool
     * @throws InvalidArgumentException
     */
    public function update(array $value, $callable = null)
    {
        if (empty($value)) {
            return false;
        }
        if (is_callable($callable)) {
            $where = new Where($this->memory);
            $callable($where);
            (new Result($where))->call(function($index, $row) use($value) {
                $row = $value + $row;
                $this->memory->asyncSet($index, $row);
            });
        } else if (null === $callable) {
            $rows = $this->memory->get();
            foreach ($rows as $index => $row) {
                $this->memory->asyncSet($index, $value + $row);
            }
        } else {
            throw new InvalidArgumentException('$callable is only callable or null.');
        }
        $this->memory->sync();
        return true;
    }

    /**
     * @param callable $callable
     * @return bool
     * @throws FailureDeletedException
     */
    public function delete(callable $callable)
    {
        $where = new Where($this->memory);
        $callable($where);
        $target = [];
        (new Result($where))->call(function($index) use(&$target) {
            $target[] = $index;
        });
        if (empty($target)) {
            return false;
        } else if (count($target) === 1) {
            if (!$this->memory->delete($target[0])) {
                throw new FailureDeletedException('fail, delete item in index ' . $target[0]);
            }
            return true;
        } else {
            if (!$this->memory->delete($target)) {
                throw new FailureDeletedException('fail, delete item in index ' . implode(',', $target));
            }
            return true;
        }
    }

    /**
     * @return bool
     */
    public function truncate()
    {
        return $this->memory->deleteAll();
    }

}