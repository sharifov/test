<?php

use modules\objectSegment\components\ObjectSegmentComponent;
use modules\objectSegment\src\contracts\ObjectSegmentKeyContract;
use modules\objectSegment\src\object\ClientSegmentObject;
use modules\objectSegment\src\object\LeadObjectSegmentObject;

return [
    'class'       => ObjectSegmentComponent::class,
    'cacheEnable' => true,
    'modules'     => [
        ObjectSegmentKeyContract::TYPE_KEY_LEAD => LeadObjectSegmentObject::class,
        ObjectSegmentKeyContract::TYPE_KEY_CLIENT => ClientSegmentObject::class,
    ],
];
