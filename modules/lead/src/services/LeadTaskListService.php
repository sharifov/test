<?php

namespace modules\lead\src\services;

use common\models\Lead;
use modules\featureFlag\FFlag;
use modules\lead\src\abac\taskLIst\LeadTaskListAbacDto;
use modules\lead\src\abac\taskLIst\LeadTaskListAbacObject;
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
    private Lead $lead;

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    /* TODO::  */

    public function assign()
    {


    }

    public function assignReAssign()
    {


    }

    public function hasActiveLeadObjectSegment(): bool
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
            ->where(['ld_lead_id' => $this->lead->id])
            ->andWhere(['ld_field_key' => LeadDataKeyDictionary::KEY_LEAD_OBJECT_SEGMENT])
            ->exists();
    }

    public function isProcessAllowed(): bool
    {
        /** @fflag FFlag::FF_KEY_LEAD_TASK_ASSIGN, Lead to task List assign checker */
        if (!\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_LEAD_TASK_ASSIGN)) {
            return false;
        }

        /** @abac $leadTaskListAbacDto, LeadTaskListAbacObject::ASSIGN_TASK, LeadTaskListAbacObject::ACTION_ACCESS, Lead to task List assign checker */
        $can = \Yii::$app->abac->can(
            new LeadTaskListAbacDto($this->lead, $this->lead->employee_id),
            LeadTaskListAbacObject::ASSIGN_TASK,
            LeadTaskListAbacObject::ACTION_ACCESS,
            $this->lead->employee
        );
        if (!$can) {
            return false;
        }

        return true;
    }

    public function getLead(): Lead
    {
        return $this->lead;
    }
}
