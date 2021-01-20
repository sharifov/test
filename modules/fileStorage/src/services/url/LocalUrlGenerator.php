<?php

namespace modules\fileStorage\src\services\url;

/**
 * Class LocalUrlGenerator
 *
 * @property string $externalUrl
 */
class LocalUrlGenerator implements UrlGenerator
{
    private string $externalUrl;

    public function __construct(string $url)
    {
        $this->externalUrl = rtrim($url, '/');
    }

    public function generate(FileInfo $file): string
    {
        return $this->publicLink($file->path);
    }

    /**
     * @param FileInfo[] $files
     * @return array[]
     */
    public function generateForExternal(array $files): array
    {
        $links = [
            'private' => [],
            'public' => []
        ];
        foreach ($files as $file) {
            $links['public'][] = $this->publicLink($file->path);
        }
        return $links;
    }

    private function publicLink(string $path): string
    {
        return $this->externalUrl . '/' . $path;
    }
}
