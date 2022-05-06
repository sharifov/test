<?php

use yii\db\Migration;

/**
 * Class m220503_065940_add_object_segment_type_lead_record
 */
class m220503_065940_add_object_segment_type_lead_record extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $this->insert(
                '{{%object_segment_type}}',
                [
                    'ost_key'         =>    \modules\objectSegment\src\contracts\ObjectSegmentKeyContract::TYPE_KEY_LEAD,
                ]
            );
            \Yii::$app->db->getSchema()->refreshTableSchema('{{%object_segment_type}}');
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220503_065940_add_object_segment_type_lead_record:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->delete('{{%object_segment_type}}', [
                'IN',
                'ost_key',
                [
                    \modules\objectSegment\src\contracts\ObjectSegmentKeyContract::TYPE_KEY_LEAD
                ]
            ]);
            \Yii::$app->db->getSchema()->refreshTableSchema('{{%object_segment_type}}');
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220503_065940_add_object_segment_type_lead_record:safeDown:Throwable'
            );
        }
    }
}
