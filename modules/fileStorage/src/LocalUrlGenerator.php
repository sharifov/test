<?php

namespace modules\fileStorage\src;

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
}
