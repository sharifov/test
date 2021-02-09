<?php

namespace modules\fileStorage\src\entity\fileStorage\events;

/**
 * Class FileUploadedByLeadEvent
 *
 * @property int $leadId
 * @property string $name
 * @property string $title
 * @property string $path
 * @property string $uid
 */
class FileUploadedByLeadEvent
{
    public int $leadId;
    public string $name;
    public string $title;
    public string $path;
    public string $uid;

    public function __construct(int $leadId, string $name, ?string $title, string $path, string $uid)
    {
        $this->leadId = $leadId;
        $this->name = $name;
        $this->title = $title ?: '';
        $this->path = $path;
        $this->uid = $uid;
    }
}
