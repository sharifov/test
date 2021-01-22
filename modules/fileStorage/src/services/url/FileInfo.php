<?php

namespace modules\fileStorage\src\services\url;

/**
 * Class FileInfo
 *
 * @property string $name
 * @property string $path
 * @property string $uid
 * @property QueryParams $queryParams
 */
class FileInfo
{
    public string $name;
    public string $path;
    public string $uid;
    public QueryParams $queryParams;

    public function __construct(string $name, string $path, string $uid, ?QueryParams $queryParams = null)
    {
        $this->name = $name;
        $this->path = $path;
        $this->uid = $uid;
        if ($queryParams === null) {
            $this->queryParams = QueryParams::byEmpty();
        } else {
            $this->queryParams = $queryParams;
        }
    }
}
