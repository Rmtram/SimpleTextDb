<?php

namespace Rmtram\SimpleTextDb\Query\Operator;

/**
 * Class GreaterThanOrEqual
 * @package Rmtram\SimpleTextDb\Query\Operator
 */
class GreaterThanOrEqual extends AbstractComparison
{
    /**
     * @var string
     */
    protected $operator = '>=';
}