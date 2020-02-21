<?php

namespace modules\qaTask\migrations;

use Yii;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use yii\db\Migration;
use yii\helpers\Json;

/**
 * Class m200221_073439_add_rule_qa_task_lead_processing_quality
 */
class m200221_073439_add_rule_qa_task_lead_processing_quality extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->insert('{{%qa_task_rules}}', [
            'tr_key' => 'qa_lead_processing_quality',
            'tr_type' => QaTaskObjectType::LEAD,
            'tr_name' => 'QA Lead Processing Quality',
            'tr_description' => null,
            'tr_parameters' => Json::encode([
                'calls_per_frame' => 2,
                'out_min_duration' => 5,
                'in_min_rec_duration' => 30,
                'include_in_calls' => false,
                'hour_offset' => 12,
                'hour_frame_1' => 24,
                'hour_frame_2' => 48,
                'hour_frame_3' => 72
            ]),
            'tr_enabled' => true,
            'tr_created_dt' => date('Y-m-d H:i:s'),
            'tr_updated_dt' => date('Y-m-d H:i:s'),
        ]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%qa_task_rules}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%qa_task_rules}}', ['IN', 'tr_key', [
            'qa_lead_processing_quality'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
