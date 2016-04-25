<?php

namespace Rmtram\SimpleTextDb\Config;

/**
 * Class Attribute
 * @package Rmtram\SimpleTextDb\Config
 */
class Attribute
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var string
     */
    private $table;
    /**
     * @var string
     */
    private $extension;

    /**
     * @var string
     */
    private $driver;

    /**
     * @throws \UnexpectedValueException
     */
    public function assert()
    {
        $assertions = ['path', 'table', 'extension'];
        foreach ($assertions as $assertion) {
            if (empty($this->{$assertion})) {
                throw new \UnexpectedValueException('bad empty property in ' . $assertion);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        if ('/' !== substr($path, 0, -1)) {
            $path .= '/';
        }
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param mixed $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return mixed
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param mixed $extension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param string $driver
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    /**
     * @return string
     */
    public function getLockPath()
    {
        return sprintf('%s%s.lock', $this->path, $this->table);
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return sprintf('%s.%s', $this->table, $this->extension);
    }

    /**
     * @return string
     */
    public function getFullPath()
    {
        return sprintf('%s%s.%s', $this->path, $this->table, $this->extension);
    }

}