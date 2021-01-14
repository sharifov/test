<?php

namespace modules\fileStorage\src\entity\fileStorage;

use sales\repositories\NotFoundException;

class FileStorageRepository
{
    public function find(int $id): FileStorage
    {
        if ($task = FileStorage::findOne($id)) {
            return $task;
        }
        throw new NotFoundException('FileStorage is not found.');
    }

    public function save(FileStorage $task): void
    {
        if (!$task->save(false)) {
            throw new \RuntimeException('FileStorage saving error.');
        }
    }

    public function remove(FileStorage $file): void
    {
        if (!$file->delete()) {
            throw new \RuntimeException('FileStorage removing error.');
        }
    }
}
