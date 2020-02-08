<?php

namespace modules\qaTask\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200208_112507_create_tbl_qa_task_status_reason
 */
class m200208_112507_create_tbl_qa_task_status_reason extends Migration
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

        $this->createTable('{{%qa_task_status_reason}}',	[
            'tsr_id' => $this->primaryKey(),
            'tsr_object_type_id' => $this->tinyInteger()->notNull(),
            'tsr_status_id' => $this->tinyInteger()->notNull(),
            'tsr_key' => $this->string(30)->unique()->notNull(),
            'tsr_name' => $this->string(30)->notNull(),
            'tsr_description' => $this->string(255)->null(),
            'tsr_comment_required' => $this->boolean()->defaultValue(false)->notNull(),
            'tsr_enabled' => $this->boolean()->defaultValue(true)->notNull(),
            'tsr_created_user_id' => $this->integer()->null(),
            'tsr_updated_user_id' => $this->integer()->null(),
            'tsr_created_dt' => $this->dateTime()->null(),
            'tsr_updated_dt' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->addForeignKey('FK-qa_task_status_reason-tsr_created_user_id', '{{%qa_task_status_reason}}', ['tsr_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-qa_task_status_reason-tsr_updated_user_id', '{{%qa_task_status_reason}}', ['tsr_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-qa_task_status_reason-tsr_updated_user_id', '{{%qa_task_status_reason}}');
        $this->dropForeignKey('FK-qa_task_status_reason-tsr_created_user_id', '{{%qa_task_status_reason}}');
        $this->dropTable('{{%qa_task_status_reason}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
