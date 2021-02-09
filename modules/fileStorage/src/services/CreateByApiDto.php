<?php

namespace modules\fileStorage\src\services;

use modules\fileStorage\src\entity\fileStorage\FileStorageStatus;
use modules\fileStorage\src\entity\fileStorage\Uid;
use modules\fileStorage\src\FileSystem;

/**
 * Class CreateByApiDto
 *
 * @property string|null $name
 * @property string|null $title
 * @property string|null $path
 * @property int|null $size
 * @property string|null $uid
 * @property string|null $mimeType
 * @property string|null $md5Hash
 * @property bool|null $private
 * @property string|null $createdDt
 * @property int|null $status
 *
 * @property FileSystem $fileSystem
 */
class CreateByApiDto
{
    public ?string $name;
    public ?string $title = null;
    public ?string $path;
    public ?int $size;
    public ?string $uid;
    public ?string $mimeType;
    public ?string $md5Hash;
    public bool $private = false;
    public ?string $createdDt;
    public int $status = FileStorageStatus::UPLOADED;

    private FileSystem $fileSystem;

    /**
     * @param string $path
     * @param FileSystem $fileSystem
     */
    public function __construct(string $path, FileSystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
        $this->path = $path;
        $this->setAll();
    }

    private function setAll(): void
    {
        $this->setName()
            ->setSize()
            ->setUid()
            ->setMimeType()
            ->setMd5Hash()
            ->setPrivate()
            ->setCreatedDt();
    }

    private function setMd5Hash(): CreateByApiDto
    {
        $this->md5Hash = md5($this->fileSystem->read($this->path));
        return $this;
    }

    private function setMimeType(): CreateByApiDto
    {
        $this->mimeType = $this->fileSystem->mimeType($this->path);
        return $this;
    }

    private function setUid(): CreateByApiDto
    {
        $this->uid = Uid::next()->getValue();
        return $this;
    }

    private function setSize(): CreateByApiDto
    {
        $this->size = $this->fileSystem->fileSize($this->path);
        return $this;
    }

    private function setName(): CreateByApiDto
    {
        $patchExploded = explode(DIRECTORY_SEPARATOR, $this->path);
        $this->name = end($patchExploded);
        return $this;
    }

    private function setPrivate(): CreateByApiDto
    {
        $visibility = $this->fileSystem->visibility($this->path);
        $this->private = ($visibility === 'private');
        return $this;
    }

    private function setCreatedDt(): CreateByApiDto
    {
        $this->createdDt = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        return $this;
    }
}
