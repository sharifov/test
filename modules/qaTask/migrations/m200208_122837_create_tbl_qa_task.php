<?php

namespace modules\qaTask\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200208_122837_create_tbl_qa_task
 */
class m200208_122837_create_tbl_qa_task extends Migration
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

        $this->createTable('{{%qa_task}}',	[
            't_id' => $this->primaryKey(),
            't_gid' => $this->string(32)->unique()->notNull(),
            't_object_type_id' => $this->tinyInteger()->notNull(),
            't_object_id' => $this->integer()->notNull(),
            't_category_id' => $this->integer()->null(),
            't_status_id' => $this->tinyInteger()->notNull(),
            't_rating' => $this->tinyInteger(1)->null(),
            't_create_type_id' => $this->tinyInteger()->null(),
            't_description' => $this->text()->null(),
            't_department_id' => $this->tinyInteger(1)->null(),
            't_deadline_dt' => $this->dateTime()->null(),
            't_assigned_user_id' => $this->integer()->null(),
            't_created_user_id' => $this->integer()->null(),
            't_updated_user_id' => $this->integer()->null(),
            't_created_dt' => $this->dateTime()->null(),
            't_updated_dt' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->addForeignKey('FK-qa_task-t_assigned_user_id', '{{%qa_task}}', ['t_assigned_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-qa_task-t_created_user_id', '{{%qa_task}}', ['t_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-qa_task-t_updated_user_id', '{{%qa_task}}', ['t_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-qa_task-t_category_id', '{{%qa_task}}', ['t_category_id'], '{{%qa_task_category}}', ['tc_id'], 'SET NULL', 'CASCADE');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-qa_task-t_category_id', '{{%qa_task}}');
        $this->dropForeignKey('FK-qa_task-t_updated_user_id', '{{%qa_task}}');
        $this->dropForeignKey('FK-qa_task-t_created_user_id', '{{%qa_task}}');
        $this->dropForeignKey('FK-qa_task-t_assigned_user_id', '{{%qa_task}}');
        $this->dropTable('{{%qa_task}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
