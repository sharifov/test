<?php

namespace modules\qaTask\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200218_150141_create_tbl_qa_task_rules
 */
class m200218_150141_create_tbl_qa_task_rules extends Migration
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

        $this->createTable('{{%qa_task_rules}}', [
            'tr_id' => $this->primaryKey(),
            'tr_key' => $this->string(30)->notNull()->unique(),
            'tr_type' => $this->tinyInteger()->notNull(),
            'tr_name' => $this->string(50)->notNull(),
            'tr_description' => $this->string(255)->null(),
            'tr_parameters' => $this->text()->null(),
            'tr_enabled' => $this->boolean()->defaultValue(true)->notNull(),
            'tr_created_user_id' => $this->integer()->null(),
            'tr_updated_user_id' => $this->integer()->null(),
            'tr_created_dt' => $this->dateTime()->null(),
            'tr_updated_dt' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-qa_task_rules-tr_created_user_id',
            '{{%qa_task_rules}}',
            'tr_created_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-qa_task_rules-tr_updated_user_id',
            '{{%qa_task_rules}}',
            'tr_updated_user_id',
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
        $this->dropForeignKey('FK-qa_task_rules-tr_updated_user_id','{{%qa_task_rules}}');
        $this->dropForeignKey('FK-qa_task_rules-tr_created_user_id','{{%qa_task_rules}}');
        $this->dropTable('{{%qa_task_rules}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
