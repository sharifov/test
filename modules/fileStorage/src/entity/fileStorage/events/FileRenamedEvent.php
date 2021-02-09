<?php

namespace modules\fileStorage\src\entity\fileStorage\events;

/**
 * Class FileRenamedEvent
 *
 * @property int $fileId
 * @property string $oldName
 * @property string $newName
 * @property string $oldPath
 * @property string $newPath
 */
class FileRenamedEvent
{
    public int $fileId;
    public string $oldName;
    public string $newName;
    public string $oldPath;
    public string $newPath;

    public function __construct(
        int $fileId,
        string $oldName,
        string $newName,
        string $oldPath,
        string $newPath
    ) {
        $this->fileId = $fileId;
        $this->oldName = $oldName;
        $this->newName = $newName;
        $this->oldPath = $oldPath;
        $this->newPath = $newPath;
    }
}
