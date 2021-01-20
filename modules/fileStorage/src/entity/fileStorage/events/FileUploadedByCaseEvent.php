<?php

namespace modules\fileStorage\src\entity\fileStorage\events;

/**
 * Class FileUploadedByCaseEvent
 *
 * @property int $caseId
 * @property string $name
 * @property string|null $title
 * @property string $path
 * @property string $uid
 */
class FileUploadedByCaseEvent
{
    public int $caseId;
    public string $name;
    public ?string $title;
    public string $path;
    public string $uid;

    public function __construct(int $caseId, string $name, ?string $title, string $path, string $uid)
    {
        $this->caseId = $caseId;
        $this->name = $name;
        $this->title = $title ?: '';
        $this->path = $path;
        $this->uid = $uid;
    }
}
