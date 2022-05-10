<?php

use modules\objectSegment\components\ObjectSegmentComponent;

return [
    'class'       => ObjectSegmentComponent::class,
    'cacheEnable' => true,
    'modules'     => [
        \modules\objectSegment\src\contracts\ObjectSegmentKeyContract::TYPE_KEY_LEAD => \modules\objectSegment\src\object\LeadObjectSegmentObject::class,
    ],
];
