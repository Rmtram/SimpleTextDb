<?php

namespace Rmtram\SimpleTextDb;

use Gaufrette\Adapter\Local;
use Gaufrette\Exception\FileNotFound;
use Gaufrette\Filesystem;
use Rmtram\SimpleTextDb\Config\Attribute;
use Rmtram\SimpleTextDb\Driver\AbstractDriver;
use Rmtram\SimpleTextDb\Exceptions\UndefinedDriverException;
use Rmtram\SimpleTextDb\Shared\Memory;

/**
 * Class Connector
 * @package Rmtram\SimpleTextDb
 */
class Connector
{

    /**
     * @var Attribute
     */
    private $attribute;

    /**
     * @var array
     */
    private $driverContainer = [
        'hash' => '\Rmtram\SimpleTextDb\Driver\HashMapDriver',
        'list' => '\Rmtram\SimpleTextDb\Driver\ListDriver'
    ];

    /**
     * @param $path
     */
    public function __construct($path)
    {
        $this->attribute = new Attribute();
        $this->attribute->setPath($path);
        $this->attribute->setExtension('sdb');
    }

    /**
     * @param $table
     * @param string $driver
     * @return AbstractDriver
     * @throws UndefinedDriverException
     */
    public function connection($table, $driver = 'list')
    {
        if (!isset($this->driverContainer[$driver])) {
            throw new UndefinedDriverException($driver);
        }
        $classDriver = $this->driverContainer[$driver];
        $this->attribute->setDriver($classDriver);
        $this->attribute->setTable($table);
        return new $classDriver($this->attribute);
    }

    /**
     * Drop table
     * @param string $table
     * @return bool
     * @throws FileNotFound
     * @throws \RuntimeException
     */
    public function drop($table)
    {
        $attribute = $this->attribute;
        $attribute->setTable($table);
        $adapter = new Local($attribute->getPath());
        $fileSystem = new Filesystem($adapter);
        $fileSystem->delete($attribute->getFileName());
        Memory::destroy($attribute);
        return true;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->attribute->getPath();
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->attribute->setPath($path);
        return $this;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->attribute->getExtension();
    }

    /**
     * change extension.
     * e.g
     * $extension = 'db';
     * $this->extension($extension);
     * @param string $extension
     * @return $this
     */
    public function setExtension($extension)
    {
        $this->attribute->setExtension($extension);
        return $this;
    }

}