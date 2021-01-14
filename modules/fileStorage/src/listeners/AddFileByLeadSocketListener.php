<?php

namespace modules\fileStorage\src\listeners;

use common\models\Notifications;
use modules\fileStorage\src\entity\fileStorage\events\FileCreatedByLeadEvent;

class AddFileByLeadSocketListener
{
    public function handle(FileCreatedByLeadEvent $event): void
    {
        Notifications::publish('addFileToFileStorageList', ['lead_id' => $event->leadId], [
            'url' => $event->path,
            'name' => $event->name,
            'title' => $event->title,
        ]);
    }
}
