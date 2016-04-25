<?php

namespace Rmtram\SimpleTextDb\Query\Operator;

/**
 * Class Existable
 * @package Rmtram\SimpleTextDb\Query\Operator
 */
trait Existable
{
    /**
     * @param string $key
     * @param array $row
     * @return bool
     */
    protected function exists($key, $row)
    {
        return array_key_exists($key, $row);
    }
}