<?php

namespace Rmtram\SimpleTextDb\Query\Operator;

/**
 * Interface OperatorInterface
 * @package Rmtram\SimpleTextDb\Query\Operator
 */
interface OperatorInterface
{
    /**
     * Evaluate whether the row data matches.
     * @param string $key
     * @param $val
     * @param $row
     * @return boolean
     */
    public function evaluate($key, $val, $row);
}