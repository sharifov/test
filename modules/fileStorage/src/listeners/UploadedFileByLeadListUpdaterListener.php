<?php

namespace modules\fileStorage\src\listeners;

use common\models\Notifications;
use modules\fileStorage\src\entity\fileStorage\events\FileUploadedByLeadEvent;
use modules\fileStorage\src\services\url\FileInfo;
use modules\fileStorage\src\services\url\QueryParams;
use modules\fileStorage\src\services\url\UrlGenerator;

/**
 * Class UploadedFileByLeadListUpdaterListener
 *
 * @property UrlGenerator $urlGenerator
 */
class UploadedFileByLeadListUpdaterListener
{
    private UrlGenerator $urlGenerator;

    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function handle(FileUploadedByLeadEvent $event): void
    {
        Notifications::publish('addFileToFileStorageList', ['lead_id' => $event->leadId], [
            'url' => $this->urlGenerator->generate(new FileInfo($event->name, $event->path, $event->uid, QueryParams::byLead())),
            'name' => $event->name,
            'title' => $event->title,
        ]);
    }
}
