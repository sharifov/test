<?php

namespace modules\fileStorage\src\entity\fileClient;

class FileClientRepository
{
    public function save(FileClient $file): void
    {
        if (!$file->save(false)) {
            throw new \RuntimeException('FileClient saving error.');
        }
    }

    public function remove(FileClient $file): void
    {
        if (!$file->delete()) {
            throw new \RuntimeException('FileClient removing error.');
        }
    }
}
