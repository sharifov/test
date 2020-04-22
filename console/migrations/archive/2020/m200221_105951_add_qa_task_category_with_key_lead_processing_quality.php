<?php

use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use yii\db\Migration;

/**
 * Class m200221_105951_add_qa_task_category_with_key_lead_processing_quality
 */
class m200221_105951_add_qa_task_category_with_key_lead_processing_quality extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%qa_task_category}}', [
            'tc_key' => 'lead_processing_quality',
            'tc_object_type_id' => QaTaskObjectType::LEAD,
            'tc_name' => 'Lead Processing Quality',
            'tc_description' => '',
            'tc_enabled' => 1,
            'tc_default' => 0,
            'tc_created_dt' => date('Y-m-d H:i:s'),
            'tc_updated_dt' => date('Y-m-d H:i:s'),
        ]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%qa_task_category}}');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%qa_task_category}}', ['IN', 'tc_key', [
            'lead_processing_quality',
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%qa_task_category}}');
    }
}
