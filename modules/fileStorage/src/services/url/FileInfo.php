<?php

namespace modules\fileStorage\src\services\url;

/**
 * Class FileInfo
 *
 * @property string $path
 * @property string $uid
 * @property QueryParams $queryParams
 */
class FileInfo
{
    public string $path;
    public string $uid;
    public QueryParams $queryParams;

    public function __construct(string $path, string $uid, ?QueryParams $queryParams)
    {
        $this->path = $path;
        $this->uid = $uid;
        if ($queryParams === null) {
            $this->queryParams = QueryParams::byEmpty();
        } else {
            $this->queryParams = $queryParams;
        }
    }
}
