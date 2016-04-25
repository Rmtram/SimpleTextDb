<?php

namespace Rmtram\SimpleTextDb\Driver;

/**
 * Class HashMapDriver
 * @package Rmtram\SimpleTextDb\Driver
 */
class HashMapDriver extends AbstractDriver
{

    /**
     * @param $key
     * @param $value
     * @return bool
     */
    public function set($key, $value)
    {
        if (is_null($key)) {
            return false;
        }
        $this->memory->set($key, $value);
        return true;
    }

    /**
     * @param string|array $key
     * @return array|null
     */
    public function get($key)
    {
        if (is_null($key)) {
            return null;
        }

        if (is_array($key)) {
            $ret = [];
            foreach ($key as $k) {
                $ret[] = $this->memory->get($k);
            }
            return !empty($ret) ? $ret : null;
        }

        return $this->memory->get($key);
    }

    /**
     * @return array|null
     */
    public function all()
    {
        return $this->memory->get();
    }

    /**
     * @param $key
     * @return bool
     */
    public function delete($key)
    {
        $bool = $this->memory->delete($key);
        return $bool;
    }

    /**
     * @return bool
     */
    public function truncate()
    {
        return $this->memory->deleteAll();
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return $this->memory->has($key);
    }

}