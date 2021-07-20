<?php

namespace modules\qaTask\migrations;

use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use Yii;
use yii\db\Migration;
use yii\helpers\Json;

/**
 * Class m210720_075713_add_new_task_rule_lead_trash_check
 */
class m210720_075713_add_new_task_rule_lead_trash_check extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%qa_task_rules}}', [
            'tr_key' => 'qa_lead_trash_check',
            'tr_type' => QaTaskObjectType::LEAD,
            'tr_name' => 'QA Lead Trash Check',
            'tr_description' => null,
            'tr_parameters' => Json::encode([
                'departments' => [],
                'projects' => [],
                'reasons' => [],
                'qa_task_category_key' => '',
            ]),
            'tr_enabled' => false,
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
            'qa_lead_trash_check'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
