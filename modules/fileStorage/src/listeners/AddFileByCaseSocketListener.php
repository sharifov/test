<?php

namespace modules\fileStorage\src\listeners;

use common\models\Notifications;
use modules\fileStorage\src\entity\fileStorage\events\FileCreatedByCaseEvent;
use modules\fileStorage\src\UrlGenerator;

/**
 * Class AddFileByCaseSocketListener
 *
 * @property UrlGenerator $urlGenerator
 */
class AddFileByCaseSocketListener
{
    private UrlGenerator $urlGenerator;

    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function handle(FileCreatedByCaseEvent $event): void
    {
        Notifications::publish('addFileToFileStorageList', ['case_id' => $event->caseId], [
            'url' => $this->urlGenerator->generate($event->path),
            'name' => $event->name,
            'title' => $event->title,
        ]);
    }
}
