<?php

use yii\db\Migration;

/**
 * Class m220211_132939_create_tbl_phone_number_redial
 */
class m220211_132939_create_tbl_phone_number_redial extends Migration
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

        $this->createTable('{{%phone_number_redial}}', [
            'pnr_id' => $this->primaryKey(),
            'pnr_project_id' => $this->integer()->notNull(),
            'pnr_phone_pattern' => $this->string(30)->notNull(),
            'pnr_pl_id' => $this->integer()->notNull(),
            'pnr_name' => $this->string(),
            'pnr_enabled' => $this->boolean(),
            'pnr_priority' => $this->smallInteger(3)->defaultValue(0),
            'pnr_created_dt' => $this->dateTime(),
            'pnr_updated_dt' => $this->dateTime(),
            'pnr_updated_user_id' => $this->integer()
        ], $tableOptions);

        $this->addForeignKey(
            'FK-phone_number_redial-pnr_project_id',
            '{{%phone_number_redial}}',
            'pnr_project_id',
            '{{%projects}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-phone_number_redial-pnr_pl_id',
            '{{%phone_number_redial}}',
            'pnr_pl_id',
            '{{%phone_list}}',
            'pl_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-phone_number_redial-pnr_updated_user_id',
            '{{%phone_number_redial}}',
            'pnr_updated_user_id',
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
        $this->dropForeignKey('FK-phone_number_redial-pnr_project_id', '{{%phone_number_redial}}');
        $this->dropForeignKey('FK-phone_number_redial-pnr_pl_id', '{{%phone_number_redial}}');
        $this->dropForeignKey('FK-phone_number_redial-pnr_updated_user_id', '{{%phone_number_redial}}');
        $this->dropTable('{{%phone_number_redial}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
