<?php

namespace modules\fileStorage\src\entity\fileStorage\events;

/**
 * Class FileUploadedByCaseEvent
 *
 * @property int $caseId
 * @property string $name
 * @property string|null $title
 * @property string $path
 */
class FileUploadedByCaseEvent
{
    public int $caseId;
    public string $name;
    public ?string $title;
    public string $path;

    public function __construct(int $caseId, string $name, ?string $title, string $path)
    {
        $this->caseId = $caseId;
        $this->name = $name;
        $this->title = $title ?: '';
        $this->path = $path;
    }
}
