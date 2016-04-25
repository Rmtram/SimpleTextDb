<?php

namespace Rmtram\SimpleTextDb\Query\Operator;

/**
 * Class LessThanOrEqual
 * @package Rmtram\SimpleTextDb\Query\Operator
 */
class LessThanOrEqual extends AbstractComparison
{
    /**
     * @var string
     */
    protected $operator = '<=';
}