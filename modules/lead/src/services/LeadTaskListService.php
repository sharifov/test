<?php

namespace modules\lead\src\services;

use common\models\Lead;
use modules\objectSegment\src\contracts\ObjectSegmentKeyContract;
use modules\objectSegment\src\entities\ObjectSegmentList;
use modules\objectSegment\src\entities\ObjectSegmentType;
use src\model\leadData\entity\LeadData;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
use yii\db\Expression;

/**
 * Class LeadTaskListService
 */
class LeadTaskListService
{
    public static function hasActiveLeadObjectSegment(Lead $lead): bool
    {
        return LeadData::find()
            ->innerJoin([
                'object_segment_list_query' => ObjectSegmentList::find()
                    ->select(['osl_key'])
                    ->innerJoin(
                        ObjectSegmentType::tableName(),
                        'osl_ost_id = ost_id AND ost_key = ' . new Expression(ObjectSegmentKeyContract::TYPE_KEY_LEAD)
                    )
                    ->andWhere(['osl_enabled' => true])
                    ->groupBy(['osl_key'])
            ], 'object_segment_list_query.osl_key = ld_field_value')
            ->where(['ld_lead_id' => $lead->id])
            ->andWhere(['ld_field_key' => LeadDataKeyDictionary::KEY_LEAD_OBJECT_SEGMENT])
            ->exists();
    }
}
