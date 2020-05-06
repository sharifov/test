<?php

namespace common\components\purifier;

use common\components\purifier\filter\Filter;
use common\components\purifier\filter\FilterShortCodeToId;
use common\components\purifier\filter\FilterShortCodeToIdUrl;
use common\components\purifier\filter\FilterShortCodeToLink;

class PurifierFilter
{
    public static function shortCodeToLink(): Filter
    {
        return new FilterShortCodeToLink();
    }

    public static function shortCodeToIdUrl(): Filter
    {
        return new FilterShortCodeToIdUrl();
    }

    public static function shortCodeToId(): Filter
    {
        return new FilterShortCodeToId();
    }
}
