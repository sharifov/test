<?php

namespace modules\fileStorage\src\entity\fileOrder;

class FileOrderRepository
{
    public function save(FileOrder $file): void
    {
        if (!$file->save(false)) {
            throw new \RuntimeException('FileOrder saving error.');
        }
    }

    public function remove(FileOrder $file): void
    {
        if (!$file->delete()) {
            throw new \RuntimeException('FileOrder removing error.');
        }
    }
}
