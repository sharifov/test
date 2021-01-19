<?php

namespace modules\fileStorage\src\services\url;

/**
 * Class AwsS3UrlGenerator
 *
 * @property string $url
 * @property bool $isPrivate
 */
class AwsS3UrlGenerator implements UrlGenerator
{
    private string $url;
    private bool $isPrivate;

    public function __construct(string $host, ?string $prefix, bool $isPrivate)
    {
        $host = rtrim($host, '/');
        $this->url = $host;
        $prefix = rtrim($prefix, '/');
        if ($prefix) {
            $this->url .= '/' . $prefix;
        }
        $this->isPrivate = $isPrivate;
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
            if ($this->isPrivate) {
                $links['private'][] = $file;
            } else {
                $links['public'][] = $this->generate($file);
            }
        }
        return $links;
    }
}
