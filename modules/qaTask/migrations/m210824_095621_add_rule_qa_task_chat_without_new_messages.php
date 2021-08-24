<?php

namespace modules\qaTask\migrations;

use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use Yii;
use yii\db\Migration;
use yii\helpers\Json;

/**
 * Class m210824_095621_add_rule_qa_task_chat_without_new_messages
 */
class m210824_095621_add_rule_qa_task_chat_without_new_messages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->insert('{{%qa_task_rules}}', [
            'tr_key' => 'qa_chat_without_new_messages',
            'tr_type' => QaTaskObjectType::CHAT,
            'tr_name' => 'QA Chat Without New Messages',
            'tr_description' => null,
            'tr_parameters' => Json::encode([
                'qa_task_category_key' => '',
                'hours_passed' => 1,
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
            'qa_chat_without_new_messages'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
