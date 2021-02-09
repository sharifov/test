<?php

namespace modules\fileStorage\src\listeners;

use modules\fileStorage\src\entity\fileStorage\events\FileRemovedEvent;

/**
 * Class RemoveFileListUpdaterListener
 */
class RemoveFileListUpdaterListener
{
    public function handle(FileRemovedEvent $event): void
    {
        //todo
    }
}
