<?php

namespace modules\fileStorage\src\entity\fileStorage\events;

/**
 * Class FileEditedEvent
 *
 * @property string|null $title
 */
class FileEditedEvent
{
    public ?string $title;

    public function __construct(?string $title)
    {
        $this->title = $title;
    }
}
