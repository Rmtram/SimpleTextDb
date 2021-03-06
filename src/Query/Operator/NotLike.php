<?php

namespace Rmtram\SimpleTextDb\Query\Operator;

/**
 * Class NotLike
 * @package Rmtram\SimpleTextDb\Query\Operator
 */
class NotLike extends AbstractLike
{
    /**
     * @param string $key
     * @param string $val
     * @param array $row
     * @return bool
     */
    public function evaluate($key, $val, $row)
    {
        if (!$this->exists($key, $row)) {
            return false;
        }
        return !parent::evaluate($key, $val, $row);
    }
}