<?php

namespace modules\fileStorage\src\listeners;

use common\models\Notifications;
use modules\fileStorage\src\entity\fileStorage\events\FileUploadedByCaseEvent;
use modules\fileStorage\src\services\url\FileInfo;
use modules\fileStorage\src\services\url\QueryParams;
use modules\fileStorage\src\services\url\UrlGenerator;

/**
 * Class UploadedFileByCaseListUpdaterListener
 *
 * @property UrlGenerator $urlGenerator
 */
class UploadedFileByCaseListUpdaterListener
{
    private UrlGenerator $urlGenerator;

    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function handle(FileUploadedByCaseEvent $event): void
    {
        Notifications::publish('addFileToFileStorageList', ['case_id' => $event->caseId], [
            'url' => $this->urlGenerator->generate(new FileInfo($event->name, $event->path, $event->uid, $event->title, QueryParams::byCase())),
            'name' => $event->name,
            'title' => $event->title,
        ]);
    }
}
