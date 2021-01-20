<?php

namespace modules\fileStorage\src\services\url;

/**
 * Class FileInfo
 *
 * @property string $path
 * @property string $uid
 */
class FileInfo
{
    public string $path;
    public string $uid;

    public function __construct(string $path, string $uid)
    {
        $this->path = $path;
        $this->uid = $uid;
    }
}
