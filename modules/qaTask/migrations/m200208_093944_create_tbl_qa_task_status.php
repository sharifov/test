<?php

namespace modules\qaTask\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200208_093944_create_tbl_qa_task_status
 */
class m200208_093944_create_tbl_qa_task_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%qa_task_status}}',	[
            'ts_id' => $this->tinyInteger()->unique()->notNull(),
            'ts_name' => $this->string(30)->notNull(),
            'ts_description' => $this->string(255)->null(),
            'ts_enabled' => $this->boolean()->defaultValue(true)->notNull(),
            'ts_css_class' => $this->string(100)->null(),
            'ts_created_user_id' => $this->integer()->null(),
            'ts_updated_user_id' => $this->integer()->null(),
            'ts_created_dt' => $this->dateTime()->null(),
            'ts_updated_dt' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->addForeignKey('FK-qa_task_status-ts_created_user_id', '{{%qa_task_status}}', ['ts_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-qa_task_status-ts_updated_user_id', '{{%qa_task_status}}', ['ts_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-qa_task_status-ts_created_user_id', '{{%qa_task_status}}');
        $this->dropForeignKey('FK-qa_task_status-ts_updated_user_id', '{{%qa_task_status}}');
        $this->dropTable('{{%qa_task_status}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
