<?php

namespace modules\fileStorage\src\services\url;

interface UrlGenerator
{
    public const TYPE_PUBLIC = 0;
    public const TYPE_PRIVATE = 1;

    public function generate(FileInfo $file): string;

    /**
     * @param FileInfo[] $files
     * @return array => ['public' => ['https://host.com/...], 'private' => ['base/path/to/file']]
     */
    public function generateForExternal(array $files): array;
}
