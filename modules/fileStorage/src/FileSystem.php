<?php

namespace modules\fileStorage\src;

use League\Flysystem\FilesystemOperator;

/**
 * Class FileSystem
 *
 * @property FilesystemOperator $filesystemOperator
 * @property Configurator $configurator
 */
class FileSystem
{
    private FilesystemOperator $filesystemOperator;
    private Configurator $configurator;

    public function __construct(FilesystemOperator $filesystemOperator, Configurator $configurator)
    {
        $this->filesystemOperator = $filesystemOperator;
        $this->configurator = $configurator;
    }

    public function writeStream(string $location, $contents, array $config = []): void
    {
        $this->filesystemOperator->writeStream($location, $contents, array_merge($this->configurator->getUploadConfig(), $config));
    }

    public function delete(string $location): void
    {
        $this->filesystemOperator->delete($location);
    }
}
