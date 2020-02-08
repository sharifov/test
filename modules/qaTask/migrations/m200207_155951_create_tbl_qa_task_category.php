<?php

namespace modules\qaTask\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200207_155951_create_tbl_qa_task_category
 */
class m200207_155951_create_tbl_qa_task_category extends Migration
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

        $this->createTable('{{%qa_task_category}}',	[
            'tc_id' => $this->primaryKey(),
            'tc_key' => $this->string(30)->unique()->notNull(),
            'tc_object_type_id' => $this->tinyInteger()->notNull(),
            'tc_name' => $this->string(30)->notNull(),
            'tc_description' => $this->string(255)->null(),
            'tc_enabled' => $this->boolean()->defaultValue(true)->notNull(),
            'tc_default' => $this->boolean()->defaultValue(false)->notNull(),
            'tc_created_user_id' => $this->integer()->null(),
            'tc_updated_user_id' => $this->integer()->null(),
            'tc_created_dt' => $this->dateTime()->notNull(),
            'tc_updated_dt' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->addForeignKey('FK-qa_task_category-tc_created_user_id', '{{%qa_task_category}}', ['tc_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-qa_task_category-tc_updated_user_id', '{{%qa_task_category}}', ['tc_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-qa_task_category-tc_updated_user_id', '{{%qa_task_category}}');
        $this->dropForeignKey('FK-qa_task_category-tc_created_user_id', '{{%qa_task_category}}');
        $this->dropTable('{{%qa_task_category}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
