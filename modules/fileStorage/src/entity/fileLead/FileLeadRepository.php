<?php

namespace modules\fileStorage\src\entity\fileLead;

class FileLeadRepository
{
    public function save(FileLead $file): void
    {
        if (!$file->save(false)) {
            throw new \RuntimeException('FileLead saving error.');
        }
    }

    public function remove(FileLead $file): void
    {
        if (!$file->delete()) {
            throw new \RuntimeException('FileLead removing error.');
        }
    }
}
