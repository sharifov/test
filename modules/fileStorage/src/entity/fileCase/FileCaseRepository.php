<?php

namespace modules\fileStorage\src\entity\fileCase;

class FileCaseRepository
{
    public function save(FileCase $file): void
    {
        if (!$file->save(false)) {
            throw new \RuntimeException('FileCase saving error.');
        }
    }

    public function remove(FileCase $file): void
    {
        if (!$file->delete()) {
            throw new \RuntimeException('FileCase removing error.');
        }
    }
}
