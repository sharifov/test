<?php

namespace modules\fileStorage\src;

use League\Flysystem\FilesystemOperator;

/**
 * Class FileSystem
 *
 * @property FilesystemOperator $filesystemOperator
 */
class FileSystem
{
    private FilesystemOperator $filesystemOperator;

    public function __construct(FilesystemOperator $filesystemOperator)
    {
        $this->filesystemOperator = $filesystemOperator;
    }

    public function writeStream(string $location, $contents, array $config = []): void
    {
        $this->filesystemOperator->writeStream($location, $contents, $config);
    }
}
