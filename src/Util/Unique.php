<?php

namespace Rmtram\SimpleTextDb\Util;

/**
 * Class Unique
 * @package Rmtram\SimpleTextDb\Util
 */
class Unique
{
    /**
     * generate unique id.
     * @return string
     */
    public static function id()
    {
        return sprintf('%d%s',
            ceil(microtime(true) * 1000),
            substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 32)
        );
    }
}