<?php

namespace Rmtram\SimpleTextDb\Shared;

use Gaufrette\Adapter\Local;
use Gaufrette\Exception\FileNotFound;
use Gaufrette\Filesystem;
use Rmtram\SimpleTextDb\Config\Attribute;
use Rmtram\SimpleTextDb\Exceptions\LockException;
use Rmtram\SimpleTextDb\Exceptions\UnMatchDriverException;

class Memory
{

    /**
     * @var array [self]
     */
    private static $instances = [];

    /**
     * @var array
     */
    private $items = [];

    /**
     * @var string
     */
    private $driver;

    /**
     * @var Attribute
     */
    private $attribute;

    /**
     * @param Attribute $attribute
     * @return Memory
     */
    public static function make(Attribute $attribute)
    {
        $attribute->assert();
        $table = $attribute->getTable();
        if (!isset(self::$instances[$table])) {
            self::$instances[$table] = new self($attribute);
        }
        return self::$instances[$table];
    }

    /**
     * @param Attribute $attribute
     * @return bool
     */
    public static function destroy(Attribute $attribute)
    {
        $table = $attribute->getTable();
        if (isset(self::$instances[$table])) {
            unset(self::$instances[$table]);
            return true;
        }
        return false;
    }

    /**
     * @param Attribute $attribute
     */
    private function __construct(Attribute $attribute)
    {
        $this->attribute = $attribute;
        $adapter = new Local($attribute->getPath());
        $this->fileSystem = new Filesystem($adapter);
        $this->create();
        $this->load();
    }

    private function create()
    {
        $fileName = $this->attribute->getFileName();
        if (!$this->fileSystem->has($fileName)) {
            $this->fileSystem->write($fileName, serialize([
                'driver' => $this->attribute->getDriver(),
                'result' => []
            ]));
        }
    }

    public function drop()
    {
        $fileName = $this->attribute->getFileName();
        if (!$this->fileSystem->has($fileName)) {
            return false;
        }
        $this->fileSystem->delete($fileName);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * @param string|null $key
     * @return array|null
     */
    public function get($key = null)
    {
        if (is_null($key)) {
            return $this->items;
        }
        if(isset($this->items[$key])) {
            return $this->items[$key];
        }
        return null;
    }

    /**
     * @param $value
     */
    public function add($value)
    {
        $this->load();
        $this->items[] = $value;
        $this->sync();
    }

    /**
     * @param array $values
     * @return bool
     * @throws UnMatchDriverException
     */
    public function bulkAdd(array $values)
    {
        if (count($values) === count($values, COUNT_RECURSIVE)) {
            return false;
        }
        $this->load();
        foreach ($values as $value) {
            $this->items[] = $value;
        }
        $this->sync();
        return true;
    }

    public function asyncSet($key, $value)
    {
        $this->items[$key] = $value;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->load();
        $this->items[$key] = $value;
        $this->sync();
    }

    /**
     * @param array $items
     */
    public function setAll(array $items)
    {
        $this->load();
        $this->items = $items;
        $this->sync();
    }

    /**
     * @param string|array $key
     * @return bool
     * @throws LockException
     */
    public function delete($key)
    {
        $this->load();
        if (is_array($key)) {
            $errors = [];
            foreach ($key as $k) {
                if (!$this->has($k)) {
                    $errors[] = $k;
                } else {
                    unset($this->items[$k]);
                }
            }
            if (empty($errors)) {
                $this->sync();
                return true;
            } else {
                $this->load();
                return false;
            }
        }
        if ($this->has($key)) {
            unset($this->items[$key]);
            $this->sync();
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function deleteAll()
    {
        if (empty($this->items)) {
            return false;
        }
        $this->items = [];
        $this->sync();
        return true;
    }

    /**
     * @throws \RuntimeException
     */
    public function sync()
    {
        $fileName = $this->attribute->getFileName();
        $context = $this->serialize();
        if (!$this->fileSystem->write($fileName, $context, true)) {
            throw new \RuntimeException('failed write file in ' . $fileName);
        }
    }

    /**
     * load context.
     * @throws \RuntimeException
     * @throws FileNotFound
     */
    public function load()
    {
        $fileName = $this->attribute->getFileName();
        $serialize = $this->fileSystem->read($fileName);
        if (!$items = unserialize($serialize)) {
            throw new \RuntimeException('damaged file in ' . $fileName);
        }
        if ($items['driver'] !== $this->attribute->getDriver()) {
            throw new UnMatchDriverException('Driver is only ' . $items['driver']);
        }
        $this->driver = $items['driver'];
        $this->items  = $items['result'];
    }

    /**
     * @return string
     */
    private function serialize()
    {
        return serialize([
            'driver' => $this->driver,
            'result' => $this->items
        ]);
    }

}