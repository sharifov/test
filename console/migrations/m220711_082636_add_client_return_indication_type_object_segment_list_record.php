<?php

use modules\objectSegment\src\contracts\ObjectSegmentKeyContract;
use modules\objectSegment\src\contracts\ObjectSegmentListContract;
use modules\objectSegment\src\entities\ObjectSegmentList;
use modules\objectSegment\src\entities\ObjectSegmentType;
use yii\db\Migration;

/**
 * Class m220711_082636_add_client_return_indication_type_object_segment_list_record
 */
class m220711_082636_add_client_return_indication_type_object_segment_list_record extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $objectSegmentType = ObjectSegmentType
                ::find()
                ->where(['ost_key' => ObjectSegmentKeyContract::TYPE_KEY_CLIENT])
                ->limit(1)
                ->one();
            $objectSegmentListRecord = ObjectSegmentList
                ::find()
                ->where(['osl_key' => ObjectSegmentListContract::OBJECT_SEGMENT_LIST_KEY_CLIENT_RETURN])
                ->limit(1)
                ->one();
            if (isset($objectSegmentListRecord)) {
                return;
            }
            $this->insert(
                '{{%object_segment_list}}',
                [
                    'osl_ost_id' => $objectSegmentType->ost_id,
                    'osl_title' => 'Client Return Indication',
                    'osl_key'         =>    ObjectSegmentListContract::OBJECT_SEGMENT_LIST_KEY_CLIENT_RETURN,
                    'osl_enabled' => true,
                    'osl_description' => 'Client Return Indication',
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
            $objectSegmentListRecord = ObjectSegmentList
                ::find()
                ->where(['ost_key' => ObjectSegmentListContract::OBJECT_SEGMENT_LIST_KEY_CLIENT_RETURN])
                ->one();
            if (isset($objectSegmentListRecord)) {
                $objectSegmentListRecord->delete();
            }
            \Yii::$app->db->getSchema()->refreshTableSchema('{{%object_segment_list}}');
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220711_082636_add_client_return_indication_type_object_segment_list_record:safeDown:Throwable'
            );
        }
    }
}
