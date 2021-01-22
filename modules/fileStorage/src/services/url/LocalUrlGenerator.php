<?php

namespace modules\fileStorage\src\services\url;

/**
 * Class LocalUrlGenerator
 *
 * @property string $internalUrl
 * @property string $externalUrl
 */
class LocalUrlGenerator implements UrlGenerator
{
    private string $internalUrl;
    private string $externalUrl;

    public function __construct(string $internalHost, string $externalUrl)
    {
        $this->internalUrl = rtrim($internalHost, '/');
        $this->externalUrl = rtrim($externalUrl, '/');
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
        $links = [];
        foreach ($files as $file) {
            $links[] = [
                'value' => $this->publicLink($file->path),
                'name' => $file->name,
                'type_id' => UrlGenerator::TYPE_PUBLIC,
            ];
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
