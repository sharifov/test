<?php

use yii\db\Migration;

/**
 * Class m220630_235618_add_lead_business_type_object_segment_list_record
 */
class m220630_235618_add_lead_business_type_object_segment_list_record extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $objectSegmentType = \modules\objectSegment\src\entities\ObjectSegmentType
                ::find()
                ->where(['ost_key' => \modules\objectSegment\src\contracts\ObjectSegmentKeyContract::TYPE_KEY_LEAD])
                ->one();
            $objectSegmentListRecord = \modules\objectSegment\src\entities\ObjectSegmentList
                ::find()
                ->where(['osl_key' => \modules\objectSegment\src\contracts\ObjectSegmentListContract::OBJECT_SEGMENT_LIST_KEY_LEAD_TYPE_BUSINESS])
                ->one();
            if (isset($objectSegmentListRecord)) {
                return;
            }
            $this->insert(
                '{{%object_segment_list}}',
                [
                    'osl_ost_id' => $objectSegmentType->ost_id,
                    'osl_title' => 'Business Lead',
                    'osl_key'         =>    \modules\objectSegment\src\contracts\ObjectSegmentListContract::OBJECT_SEGMENT_LIST_KEY_LEAD_TYPE_BUSINESS,
                    'osl_enabled' => true,
                    'osl_description' => 'Business Lead type',
                    'osl_is_system' => true,
                ]
            );
            \Yii::$app->db->getSchema()->refreshTableSchema('{{%object_segment_list}}');
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220630_235618_add_lead_business_type_object_segment_list_record:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $objectSegmentListRecord = \modules\objectSegment\src\entities\ObjectSegmentList
                ::find()
                ->where(['ost_key' => \modules\objectSegment\src\contracts\ObjectSegmentListContract::OBJECT_SEGMENT_LIST_KEY_LEAD_TYPE_BUSINESS])
                ->one();
            if (isset($objectSegmentListRecord)) {
                $objectSegmentListRecord->delete();
            }
            \Yii::$app->db->getSchema()->refreshTableSchema('{{%object_segment_list}}');
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220630_235618_add_lead_business_type_object_segment_list_record:safeDown:Throwable'
            );
        }
    }
}
