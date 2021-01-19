<?php

namespace modules\fileStorage\src\services\url;

/**
 * Class LocalUrlGenerator
 *
 * @property string $url
 */
class LocalUrlGenerator implements UrlGenerator
{
    private string $url;

    public function __construct(string $url)
    {
        $this->url = rtrim($url, '/');
    }

    public function generate(string $path): string
    {
        return $this->url . '/' . $path;
    }

    public function generateForExternal(array $files): array
    {
        $links = [
            'private' => [],
            'public' => []
        ];
        foreach ($files as $file) {
            $links['public'][] = $this->generate($file);
        }
        return $links;
    }
}
