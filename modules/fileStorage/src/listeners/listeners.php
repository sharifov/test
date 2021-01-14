<?php

use modules\fileStorage\src\entity\fileStorage\events\FileCreatedByLeadEvent;
use modules\fileStorage\src\listeners\AddFileByLeadSocketListener;

return [
    FileCreatedByLeadEvent::class => [AddFileByLeadSocketListener::class],
];
