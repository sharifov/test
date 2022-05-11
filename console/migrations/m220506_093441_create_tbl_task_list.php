<?php

use yii\db\Migration;

/**
 * Class m220506_093441_create_tbl_task_list
 */
class m220506_093441_create_tbl_task_list extends Migration
{
    /**
     * @return void
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%task_list}}', [
            'tl_id' => $this->primaryKey(),
            'tl_title' => $this->string(255)->notNull(),
            'tl_object' => $this->string(255)->notNull(),
            'tl_condition' => $this->string(1000),
            'tl_condition_json' => $this->json(),
            'tl_params_json' => $this->json(),
            'tl_work_start_time_utc' => $this->time(),
            'tl_work_end_time_utc' => $this->time(),
            'tl_duration_min' => $this->integer()->unsigned(),
            'tl_enable_type' => $this->tinyInteger(1)->notNull(),
            'tl_cron_expression' => $this->string(100),
            'tl_sort_order' => $this->smallInteger()->defaultValue(0),
            'tl_updated_dt' => $this->dateTime(),
            'tl_updated_user_id' => $this->integer()
        ], $tableOptions);

        $this->addForeignKey(
            'FK-task_list-tl_updated_user_id',
            '{{%task_list}}',
            'tl_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createIndex('IND-task_list-tl_object', '{{%task_list}}', 'tl_object');
    }

    /**
     * @return void
     */
    public function safeDown()
    {
        $this->dropTable('{{%task_list}}');
    }
}
