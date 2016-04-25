<?php

namespace Rmtram\SimpleTextDb\TestCase\Drivers;

use Rmtram\SimpleTextDb\Connector;

abstract class AbstractDriverTest extends \PHPUnit_Framework_TestCase
{

    protected function connector()
    {
        return new Connector(__DIR__ . '/../fixtures');
    }

    protected function reset()
    {
        $connector = $this->connector();
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(__DIR__ . '/../fixtures',
                \FilesystemIterator::CURRENT_AS_FILEINFO |
                \FilesystemIterator::KEY_AS_PATHNAME |
                \FilesystemIterator::SKIP_DOTS
            ),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->getExtension() === $ext = $connector->getExtension()) {
                $table = str_replace('.' . $ext, '', $file->getFilename());
                $connector->drop($table);
            }
        }
    }

    public function tearDown()
    {
        $this->reset();
    }
}