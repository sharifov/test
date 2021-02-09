<?php

namespace modules\fileStorage\src\entity\fileStorage\events;

/**
 * Class FileEditedEvent
 *
 * @property int $fileId
 * @property string|null $title
 */
class FileEditedEvent
{
    public int $fileId;
    public ?string $title;

    public function __construct(int $fileId, ?string $title)
    {
        $this->fileId = $fileId;
        $this->title = $title;
    }
}
