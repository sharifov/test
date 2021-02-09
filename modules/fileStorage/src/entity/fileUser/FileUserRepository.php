<?php

namespace modules\fileStorage\src\entity\fileUser;

class FileUserRepository
{
    public function save(FileUser $file): void
    {
        if (!$file->save(false)) {
            throw new \RuntimeException('FileUser saving error.');
        }
    }

    public function remove(FileUser $file): void
    {
        if (!$file->delete()) {
            throw new \RuntimeException('FileUser removing error.');
        }
    }
}
