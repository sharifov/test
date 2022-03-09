<?php

namespace modules\qaTask\migrations;

use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use Yii;
use yii\db\Migration;
use yii\helpers\Json;

/**
 * Class m220309_091852_create_rule_qa_lead_close_check
 */
class m220309_091852_create_rule_qa_lead_close_check extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%qa_task_rules}}', [
            'tr_key' => 'qa_lead_close_check',
            'tr_type' => QaTaskObjectType::LEAD,
            'tr_name' => 'QA Lead Close check',
            'tr_description' => null,
            'tr_parameters' => Json::encode([
                'departments' => [],
                'projects' => [],
                'qa_task_category_key' => 'lead_basic_check',
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
            'qa_lead_close_check'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
