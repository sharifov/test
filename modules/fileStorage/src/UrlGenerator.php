<?php

namespace modules\fileStorage\src;

interface UrlGenerator
{
    public function generate(string $path): string;
}
