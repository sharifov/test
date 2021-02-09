<?php

namespace modules\fileStorage\src\entity\fileStorage\events;

/**
 * Class FileEditedEvent
 *
 * @property int $fileId
 */
class FileFailedEvent
{
    public int $fileId;

    public function __construct(int $fileId)
    {
        $this->fileId = $fileId;
    }
}
