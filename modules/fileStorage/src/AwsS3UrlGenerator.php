<?php

namespace modules\fileStorage\src;

/**
 * Class AwsS3UrlGenerator
 *
 * @property string $url
 */
class AwsS3UrlGenerator implements UrlGenerator
{
    private string $url;

    public function __construct(string $host, ?string $prefix)
    {
        $host = rtrim($host, '/');
        $this->url = $host;
        $prefix = rtrim($prefix, '/');
        if ($prefix) {
            $this->url .= '/' . $prefix;
        }
    }

    public function generate(string $path): string
    {
        return $this->url . '/' . $path;
    }
}
