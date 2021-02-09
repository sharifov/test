<?php

namespace modules\fileStorage\src;

use League\Flysystem\FilesystemOperator;
use modules\fileStorage\src\services\configurator\Configurator;

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

    /**
     * @param string $location
     * @param $contents
     * @param array $config
     * @throws \League\Flysystem\FilesystemException
     */
    public function writeStream(string $location, $contents, array $config = []): void
    {
        $this->filesystemOperator->writeStream($location, $contents, array_merge($this->configurator->getUploadConfig(), $config));
    }

    /**
     * @param string $location
     * @return resource
     * @throws \League\Flysystem\FilesystemException
     */
    public function readStream(string $location)
    {
        return $this->filesystemOperator->readStream($location);
    }

    /**
     * @param string $location
     * @throws \League\Flysystem\FilesystemException
     */
    public function delete(string $location): void
    {
        $this->filesystemOperator->delete($location);
    }

    /**
     * @param string $oldLocation
     * @param string $newLocation
     * @throws \League\Flysystem\FilesystemException
     */
    public function rename(string $oldLocation, string $newLocation): void
    {
        $this->filesystemOperator->move($oldLocation, $newLocation);
    }

    /**
     * @param string $location
     * @return string
     * @throws \League\Flysystem\FilesystemException
     */
    public function read(string $location): string
    {
        return $this->filesystemOperator->read($location);
    }

    /**
     * @param string $location
     * @return bool
     * @throws \League\Flysystem\FilesystemException
     */
    public function fileExists(string $location): bool
    {
        return $this->filesystemOperator->fileExists($location);
    }

    /**
     * @param string $location
     * @return int
     * @throws \League\Flysystem\FilesystemException
     */
    public function fileSize(string $location): int
    {
        return $this->filesystemOperator->fileSize($location);
    }

    /**
     * @param string $location
     * @return string
     * @throws \League\Flysystem\FilesystemException
     */
    public function mimeType(string $location): string
    {
        return $this->filesystemOperator->mimeType($location);
    }

    /**
     * @param string $location
     * @return string
     * @throws \League\Flysystem\FilesystemException
     */
    public function visibility(string $location): string
    {
        return $this->filesystemOperator->visibility($location);
    }
}
