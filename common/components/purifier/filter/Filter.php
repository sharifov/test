<?php

namespace common\components\purifier\filter;

interface Filter
{
    public function filter(?string $content): ?string;
}
