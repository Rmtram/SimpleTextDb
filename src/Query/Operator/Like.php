<?php

namespace Rmtram\SimpleTextDb\Query\Operator;

/**
 * Class Like
 * @package Rmtram\SimpleTextDb\Query\Operator
 */
class Like extends AbstractLike
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
        return parent::evaluate($key, $val, $row);
    }
}