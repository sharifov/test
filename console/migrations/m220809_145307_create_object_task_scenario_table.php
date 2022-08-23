<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%object_task_scenario}}`.
 */
class m220809_145307_create_object_task_scenario_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%object_task_scenario}}', [
            'ots_id' => $this->primaryKey(),
            'ots_key' => $this->string()->notNull(),
            'ots_data_json' => $this->json(),
            'ots_updated_dt' => $this->dateTime(),
            'ots_updated_user_id' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%object_task_scenario}}');
    }
}
