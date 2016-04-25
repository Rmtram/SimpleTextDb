<?php

namespace Rmtram\SimpleTextDb\Query\Operator;

/**
 * Class NotEqual
 * @package Rmtram\SimpleTextDb\Query\Operator
 */
class NotEqual extends AbstractComparison
{
    /**
     * @var string
     */
    protected $operator = '!=';
}