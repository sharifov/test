<?php

namespace modules\fileStorage\src\services\url;

/**
 * Class AwsS3UrlGenerator
 *
 * @property string $internalUrl
 * @property string $externalUrl
 * @property bool $isPrivate
 */
class AwsS3UrlGenerator implements UrlGenerator
{
    private string $internalUrl;
    private string $externalUrl;
    private bool $isPrivate;

    public function __construct(string $internalHost, string $cdnHost, ?string $cdnPrefix, bool $isPrivate)
    {
        $this->internalUrl = rtrim($internalHost, '/');
        $host = rtrim($cdnHost, '/');
        $this->externalUrl = $host;
        $prefix = rtrim($cdnPrefix, '/');
        if ($prefix) {
            $this->externalUrl .= '/' . $prefix;
        }
        $this->isPrivate = $isPrivate;
    }

    public function generate(FileInfo $file): string
    {
        if ($this->isPrivate) {
            return $this->privateLink('uid=' . $file->uid . $file->queryParams->build());
        }
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
            if ($this->isPrivate) {
                $links['private'][] = $file->path;
            } else {
                $links['public'][] = $this->publicLink($file->path);
            }
        }
        return $links;
    }

    private function publicLink(string $path): string
    {
        return $this->externalUrl . '/' . $path;
    }

    private function privateLink(string $link): string
    {
        return $this->internalUrl . '/file-storage/get/view?' . $link;
    }
}
