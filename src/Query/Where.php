<?php

namespace Rmtram\SimpleTextDb\Query;

use Rmtram\SimpleTextDb\Exceptions\InvalidArgumentException;
use Rmtram\SimpleTextDb\Exceptions\NotOperatorClassException;
use Rmtram\SimpleTextDb\Exceptions\UndefinedOperatorClassException;
use Rmtram\SimpleTextDb\Query\Operator\OperatorInterface;
use Rmtram\SimpleTextDb\Shared\Memory;

/**
 * Class Where
 * @package Rmtram\SimpleTextDb\Query
 * @method $this eq(string $key, mixed $val)
 * @method $this orEq(string $key, mixed $val)
 * @method $this notEq(string $key, mixed $val)
 * @method $this orNotEq(string $key, mixed $val)
 * @method $this gt(string $key, int $val)
 * @method $this orGt(string $key, int $val)
 * @method $this gte(string $key, int $val)
 * @method $this orGte(string $key, int $val)
 * @method $this lt(string $key, int $val)
 * @method $this orLt(string $key, int $val)
 * @method $this lte(string $key, int $val)
 * @method $this orLte(string $key, int $val)
 * @method $this like(string $key, string $val)
 * @method $this orLike(string $key, string $val)
 * @method $this notLike(string $key, string $val)
 * @method $this orNotLike(string $key, string $val)
 */
class Where
{

    const OPERATOR_EQUAL                 = 0;

    const OPERATOR_NOT_EQUAL             = 1;

    const OPERATOR_LIKE                  = 2;

    const OPERATOR_NOT_LIKE              = 3;

    const OPERATOR_GREATER_THAN          = 4;

    const OPERATOR_GREATER_THAN_OR_EQUAL = 5;

    const OPERATOR_LESS_THAN             = 6;

    const OPERATOR_LESS_THAN_OR_EQUAL    = 7;

    const COMPARE_AND = 0;

    const COMPARE_OR  = 1;

    /**
     * @var array
     */
    private $operators = [
        self::OPERATOR_EQUAL                 => 'Rmtram\SimpleTextDb\Query\Operator\Equal',
        self::OPERATOR_NOT_EQUAL             => 'Rmtram\SimpleTextDb\Query\Operator\NotEqual',
        self::OPERATOR_LIKE                  => 'Rmtram\SimpleTextDb\Query\Operator\Like',
        self::OPERATOR_NOT_LIKE              => 'Rmtram\SimpleTextDb\Query\Operator\NotLike',
        self::OPERATOR_LESS_THAN             => 'Rmtram\SimpleTextDb\Query\Operator\LessThan',
        self::OPERATOR_LESS_THAN_OR_EQUAL    => 'Rmtram\SimpleTextDb\Query\Operator\LessThanOrEqual',
        self::OPERATOR_GREATER_THAN          => 'Rmtram\SimpleTextDb\Query\Operator\GreaterThan',
        self::OPERATOR_GREATER_THAN_OR_EQUAL => 'Rmtram\SimpleTextDb\Query\Operator\GreaterThanOrEqual'
    ];

    /**
     * @var array
     */
    private $where = [];

    /**
     * @var Memory
     */
    private $memory;

    /**
     * @var array
     */
    private $methods = [
        'eq'        => [self::OPERATOR_EQUAL,                 self::COMPARE_AND],
        'notEq'     => [self::OPERATOR_NOT_EQUAL,             self::COMPARE_AND],
        'gt'        => [self::OPERATOR_GREATER_THAN,          self::COMPARE_AND],
        'gte'       => [self::OPERATOR_GREATER_THAN_OR_EQUAL, self::COMPARE_AND],
        'lt'        => [self::OPERATOR_LESS_THAN,             self::COMPARE_AND],
        'lte'       => [self::OPERATOR_LESS_THAN_OR_EQUAL,    self::COMPARE_AND],
        'like'      => [self::OPERATOR_LIKE,                  self::COMPARE_AND],
        'notLike'   => [self::OPERATOR_NOT_LIKE,              self::COMPARE_AND],
        'orEq'      => [self::OPERATOR_EQUAL,                 self::COMPARE_OR],
        'orNotEq'   => [self::OPERATOR_NOT_EQUAL,             self::COMPARE_OR],
        'orGt'      => [self::OPERATOR_GREATER_THAN,          self::COMPARE_OR],
        'orGte'     => [self::OPERATOR_GREATER_THAN_OR_EQUAL, self::COMPARE_OR],
        'orLt'      => [self::OPERATOR_LESS_THAN,             self::COMPARE_OR],
        'orLte'     => [self::OPERATOR_LESS_THAN_OR_EQUAL,    self::COMPARE_OR],
        'orLike'    => [self::OPERATOR_LIKE,                  self::COMPARE_OR],
        'orNotLike' => [self::OPERATOR_NOT_LIKE,              self::COMPARE_OR]
    ];

    /**
     * @param Memory $memory
     */
    public function __construct(Memory $memory)
    {
        $this->memory = $memory;
    }

    /**
     * @param $callName
     * @param $args
     * @return $this
     * @throws InvalidArgumentException
     */
    public function __call($callName, $args)
    {
        $count = count($args);
        if ($count !== 2) {
            throw new InvalidArgumentException(sprintf(
                'expects at most 2 parameters, %d given',
                $count
            ));
        }
        if (!isset($this->methods[$callName])) {
            throw new \BadMethodCallException('undefined method in ' . $callName);
        }
        $method = $this->methods[$callName];
        $this->where[] = [$args[0], $args[1], $method[0], $method[1]];
        return $this;
    }

    /**
     * @param callable $callable
     * @throws NotOperatorClassException
     * @throws UndefinedOperatorClassException
     */
    private function call(callable $callable)
    {
        $rows = $this->memory->get();
        foreach ($rows as $index => $row) {
            $bool = true;
            foreach ($this->where as $where) {
                $operator = $this->getOperator($where[2]);
                $compare = $where[3];
                if ($operator->evaluate($where[0], $where[1], $row)) {
                    if (self::COMPARE_OR === $compare) {
                        $bool = true;
                        break;
                    }
                } else {
                    $bool = false;
                }
            }
            if (true === $bool) {
                $callable($index, $row);
            }
        }
    }

    /**
     * @param int $operatorNumber
     * @return OperatorInterface
     * @throws NotOperatorClassException
     * @throws UndefinedOperatorClassException
     */
    private function getOperator($operatorNumber)
    {
        if (!isset($this->operators[$operatorNumber])) {
            throw new UndefinedOperatorClassException('undefined operator => ' . $operatorNumber);
        }
        $operator = $this->operators[$operatorNumber];
        if (!$operator instanceof OperatorInterface) {
            $operatorClassName = $operator;
            $operator = new $operator();
            if (!$operator instanceof OperatorInterface) {
                throw new NotOperatorClassException($operatorClassName);
            }
            $this->operators[$operatorNumber] = $operator;
        }
        return $operator;
    }

}