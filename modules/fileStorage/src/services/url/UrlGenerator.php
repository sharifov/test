<?php

namespace modules\fileStorage\src\services\url;

interface UrlGenerator
{
    public function generate(string $path): string;

    /**
     * @param array $files
     * @return array ['public' => ['https://host.com/...], 'private' => ['base/path/to/file']]
     */
    public function generateForExternal(array $files): array;
}
