<?php

use modules\fileStorage\src\entity\fileStorage\events\FileRemovedEvent;
use modules\fileStorage\src\entity\fileStorage\events\FileUploadedByCaseEvent;
use modules\fileStorage\src\entity\fileStorage\events\FileUploadedByLeadEvent;
use modules\fileStorage\src\listeners\RemoveFileListUpdaterListener;
use modules\fileStorage\src\listeners\UploadedFileByCaseListUpdaterListener;
use modules\fileStorage\src\listeners\UploadedFileByLeadListUpdaterListener;

return [
    FileUploadedByLeadEvent::class => [UploadedFileByLeadListUpdaterListener::class],
    FileUploadedByCaseEvent::class => [UploadedFileByCaseListUpdaterListener::class],
    FileRemovedEvent::class => [RemoveFileListUpdaterListener::class],
];
