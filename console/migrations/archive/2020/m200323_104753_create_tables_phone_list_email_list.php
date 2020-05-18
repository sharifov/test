<?php

use yii\db\Migration;

/**
 * Class m200323_104753_create_tables_phone_list_email_list
 */
class m200323_104753_create_tables_phone_list_email_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%phone_list}}', [
            'pl_id' => $this->primaryKey(),
            'pl_phone_number' => $this->string(20)->notNull(),
            'pl_title' => $this->string(50)->null(),
            'pl_enabled' => $this->boolean()->defaultValue(true)->notNull(),
            'pl_created_user_id' => $this->integer()->null(),
            'pl_updated_user_id' => $this->integer()->null(),
            'pl_created_dt' => $this->dateTime()->null(),
            'pl_updated_dt' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->createIndex('IND-phone_list-pl_phone_number', '{{%phone_list}}', ['pl_phone_number'], true);
        $this->createIndex('IND-phone_list-pl_enabled', '{{%phone_list}}', ['pl_enabled']);

        $this->addForeignKey('FK-phone_list-pl_created_user_id', '{{%phone_list}}', ['pl_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-phone_list-pl_updated_user_id', '{{%phone_list}}', ['pl_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        $this->createTable('{{%email_list}}', [
            'el_id' => $this->primaryKey(),
            'el_email' => $this->string(160)->notNull(),
            'el_title' => $this->string(50)->null(),
            'el_enabled' => $this->boolean()->defaultValue(true)->notNull(),
            'el_created_user_id' => $this->integer()->null(),
            'el_updated_user_id' => $this->integer()->null(),
            'el_created_dt' => $this->dateTime()->null(),
            'el_updated_dt' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->createIndex('IND-email_list-el_email', '{{%email_list}}', ['el_email'], true);
        $this->createIndex('IND-email_list-el_enabled', '{{%email_list}}', ['el_enabled']);

        $this->addForeignKey('FK-email_list-el_created_user_id', '{{%email_list}}', ['el_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-email_list-el_updated_user_id', '{{%email_list}}', ['el_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%phone_list}}');
        $this->dropTable('{{%email_list}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
