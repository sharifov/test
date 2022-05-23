<?php

use yii\db\Migration;

/**
 * Class m220503_135147_add_lead_data_key_record_for_object_segment
 */
class m220503_135147_add_lead_data_key_record_for_object_segment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $this->delete('{{%lead_data_key}}', [
                'IN',
                'ldk_key',
                [
                    \src\model\leadDataKey\services\LeadDataKeyDictionary::KEY_LEAD_OBJECT_SEGMENT
                ]
            ]);
            $this->insert(
                '{{%lead_data_key}}',
                [
                    'ldk_key'         =>                   \src\model\leadDataKey\services\LeadDataKeyDictionary::KEY_LEAD_OBJECT_SEGMENT,
                    'ldk_name' => 'Lead Object Segment',
                    'ldk_enable' => true,

                ]
            );
            \Yii::$app->db->getSchema()->refreshTableSchema('{{%lead_data_key}}');
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220503_135147_add_lead_data_key_record_for_object_segment:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->delete('{{%lead_data_key}}', [
                'IN',
                'ldk_key',
                [
                    \src\model\leadDataKey\services\LeadDataKeyDictionary::KEY_LEAD_OBJECT_SEGMENT
                ]
            ]);
            \Yii::$app->db->getSchema()->refreshTableSchema('{{%lead_data_key}}');
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220503_135147_add_lead_data_key_record_for_object_segment:safeDown:Throwable'
            );
        }
    }
}
