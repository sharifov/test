<?php

namespace modules\qaTask\migrations;

use Yii;
use yii\db\Migration;


/**
 * Class m200208_171810_create_tbl_qa_task_status_log
 */
class m200208_171810_create_tbl_qa_task_status_log extends Migration
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

        $this->createTable('{{%qa_task_status_log}}', [
            'tsl_id' => $this->primaryKey(),
            'tsl_task_id' => $this->integer()->notNull(),
            'tsl_start_status_id' => $this->integer()->null(),
            'tsl_end_status_id' => $this->integer()->notNull(),
            'tsl_start_dt' => $this->dateTime()->notNull(),
            'tsl_end_dt' => $this->dateTime()->null(),
            'tsl_duration' => $this->integer()->null(),
            'tsl_reason_id' => $this->integer()->null(),
            'tsl_description' => $this->string()->null(),
            'tsl_assigned_user_id' => $this->integer()->null(),
            'tsl_created_user_id' => $this->integer()->null(),
            'tsl_action_id' => $this->integer()->null(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-qa_task_status_log-tsl_task_id',
            '{{%qa_task_status_log}}',
            'tsl_task_id',
            '{{%qa_task}}',
            't_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-qa_task_status_log-tsl_assigned_user_id',
            '{{%qa_task_status_log}}',
            'tsl_assigned_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-qa_task_status_log-tsl_created_user_id',
            '{{%qa_task_status_log}}',
            'tsl_created_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-qa_task_status_log-tsl_created_user_id', '{{%qa_task_status_log}}');
        $this->dropForeignKey('FK-qa_task_status_log-tsl_assigned_user_id', '{{%qa_task_status_log}}');
        $this->dropForeignKey('FK-qa_task_status_log-tsl_task_id', '{{%qa_task_status_log}}');
        $this->dropTable('{{%qa_task_status_log}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
