<?php

namespace modules\fileStorage\src\entity\fileStorage\events;

use modules\fileStorage\src\entity\fileStorage\FileStorageRelations;

/**
 * Class FileRemovedEvent
 *
 * @property int $fileId
 * @property FileStorageRelations $relations
 */
class FileRemovedEvent
{
    public int $fileId;
    public FileStorageRelations $relations;

    public function __construct(int $fileId, FileStorageRelations $relations)
    {
        $this->fileId = $fileId;
        $this->relations = $relations;
    }
}
