<?php

namespace modules\fileStorage\src\listeners;

use common\models\Notifications;
use modules\fileStorage\src\entity\fileStorage\events\FileCreatedByLeadEvent;
use modules\fileStorage\src\services\url\UrlGenerator;

/**
 * Class AddFileByLeadSocketListener
 *
 * @property UrlGenerator $urlGenerator
 */
class AddFileByLeadSocketListener
{
    private UrlGenerator $urlGenerator;

    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function handle(FileCreatedByLeadEvent $event): void
    {
        Notifications::publish('addFileToFileStorageList', ['lead_id' => $event->leadId], [
            'url' => $this->urlGenerator->generate($event->path),
            'name' => $event->name,
            'title' => $event->title,
        ]);
    }
}
