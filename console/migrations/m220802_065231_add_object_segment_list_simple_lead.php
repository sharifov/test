<?php

use yii\db\Migration;

/**
 * Class m220802_065231_add_object_segment_list_simple_lead
 */
class m220802_065231_add_object_segment_list_simple_lead extends Migration
{
    private string $ostKeyLead = \modules\objectSegment\src\contracts\ObjectSegmentKeyContract::TYPE_KEY_LEAD;
    private string $segmentKeySimpleLead = \modules\objectSegment\src\contracts\ObjectSegmentListContract::OBJECT_SEGMENT_LIST_KEY_LEAD_TYPE_SIMPLE;

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $objectSegmentType = \modules\objectSegment\src\entities\ObjectSegmentType::find()
                ->where(['ost_key' => $this->ostKeyLead])
                ->one();
            if (!$objectSegmentType) {
                throw new \RuntimeException('ObjectSegmentType not found by (' . $this->ostKeyLead . ')');
            }

            $objectSegmentListLeadSimple = \modules\objectSegment\src\entities\ObjectSegmentList::find()
                ->where(['osl_key' => $this->segmentKeySimpleLead])
                ->one();
            if (isset($objectSegmentListLeadSimple)) {
                return;
            }

            $this->insert(
                '{{%object_segment_list}}',
                [
                    'osl_ost_id' => $objectSegmentType->ost_id,
                    'osl_title' => 'Default Simple Lead',
                    'osl_key' => $this->segmentKeySimpleLead,
                    'osl_enabled' => true,
                    'osl_description' => 'Default Lead type',
                    'osl_is_system' => true,
                ]
            );
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220802_065231_add_object_segment_list_simple_lead:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            \modules\objectSegment\src\entities\ObjectSegmentList::deleteAll(['osl_key' => $this->segmentKeySimpleLead]);
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220802_065231_add_object_segment_list_simple_lead:safeDown:Throwable'
            );
        }
    }
}
