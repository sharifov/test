<?php

namespace modules\fileStorage\src\entity\fileStorage\events;

/**
 * Class FileUploadedByLeadEvent
 *
 * @property int $leadId
 * @property string $name
 * @property string|null $title
 * @property string $path
 */
class FileUploadedByLeadEvent
{
    public int $leadId;
    public string $name;
    public ?string $title;
    public string $path;

    public function __construct(int $leadId, string $name, ?string $title, string $path)
    {
        $this->leadId = $leadId;
        $this->name = $name;
        $this->title = $title ?: '';
        $this->path = $path;
    }
}
