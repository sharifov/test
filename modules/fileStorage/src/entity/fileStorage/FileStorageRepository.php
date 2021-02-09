<?php

namespace modules\fileStorage\src\entity\fileStorage;

use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class FileStorageRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class FileStorageRepository
{
    private EventDispatcher $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): FileStorage
    {
        if ($file = FileStorage::findOne($id)) {
            return $file;
        }
        throw new NotFoundException('FileStorage is not found.');
    }

    public function save(FileStorage $file): void
    {
        if (!$file->save(false)) {
            throw new \RuntimeException('FileStorage saving error.');
        }
        $this->eventDispatcher->dispatchAll($file->releaseEvents());
    }

    public function remove(FileStorage $file): void
    {
        if (!$file->delete()) {
            throw new \RuntimeException('FileStorage removing error.');
        }
        $this->eventDispatcher->dispatchAll($file->releaseEvents());
    }
}
