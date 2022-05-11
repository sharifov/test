<?php

namespace modules\objectSegment\components;

use modules\objectSegment\src\contracts\ObjectSegmentAssigmentServiceInterface;
use modules\objectSegment\src\contracts\ObjectSegmentKeyContract;
use modules\objectSegment\src\service\lead\LeadObjectSegmentAssignService;

class ObjectSegmentAssigmentFactory
{
    /**
     * @param string $objectType
     * @return ObjectSegmentAssigmentServiceInterface
     * @throws \yii\base\InvalidConfigException
     */
    public static function getService(string $objectType): ObjectSegmentAssigmentServiceInterface
    {
        switch ($objectType) {
            case ObjectSegmentKeyContract::TYPE_KEY_LEAD:
                return \Yii::createObject(LeadObjectSegmentAssignService::class);
                break;
            default:
                throw new \DomainException('Object Type is not valid');
        }
    }
}
