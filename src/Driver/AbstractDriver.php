<?php

namespace Rmtram\SimpleTextDb\Driver;

use Rmtram\SimpleTextDb\Config\Attribute;
use Rmtram\SimpleTextDb\Shared\Memory;

/**
 * Class AbstractDriver
 * @package Rmtram\SimpleTextDb\Driver
 */
abstract class AbstractDriver
{

    /**
     * @var Attribute
     */
    protected $attribute;

    /**
     * @var Memory
     */
    protected $memory;

    /**
     * @param Attribute $attribute
     */
    public function __construct(Attribute $attribute)
    {
        $attribute->assert();
        $this->attribute = $attribute;
        $this->memory = Memory::make($this->attribute);
    }

}