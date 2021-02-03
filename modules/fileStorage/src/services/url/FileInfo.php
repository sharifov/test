<?php

namespace modules\fileStorage\src\services\url;

/**
 * Class FileInfo
 *
 * @property string $name
 * @property string $path
 * @property string $uid
 * @property string $title
 * @property QueryParams $queryParams
 */
class FileInfo
{
    public string $name;
    public string $path;
    public string $uid;
    public string $title;
    public QueryParams $queryParams;

    public function __construct(
        string $name,
        string $path,
        string $uid,
        ?string $title,
        ?QueryParams $queryParams
    ) {
        $this->name = $name;
        $this->path = $path;
        $this->uid = $uid;
        $this->title = $title ?: '';
        if ($queryParams === null) {
            $this->queryParams = QueryParams::byEmpty();
        } else {
            $this->queryParams = $queryParams;
        }
    }
}
