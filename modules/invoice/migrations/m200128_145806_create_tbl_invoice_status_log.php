<?php

namespace modules\invoice\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200128_145806_create_tbl_invoice_status_log
 */
class m200128_145806_create_tbl_invoice_status_log extends Migration
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

        $this->createTable('{{%invoice_status_log}}', [
            'invsl_id' => $this->primaryKey(),
            'invsl_invoice_id' => $this->integer()->notNull(),
            'invsl_start_status_id' => $this->tinyInteger(),
            'invsl_end_status_id' => $this->tinyInteger()->notNull(),
            'invsl_start_dt' => $this->dateTime()->notNull(),
            'invsl_end_dt' => $this->dateTime(),
            'invsl_duration' => $this->integer(),
            'invsl_description' => $this->string(255),
            'invsl_action_id' => $this->tinyInteger(),
            'invsl_created_user_id' => $this->integer(),

        ], $tableOptions);

        $this->addForeignKey(
            'FK-invoice_status_log_invsl_invoice_id',
            '{{%invoice_status_log}}',
            'invsl_invoice_id',
            '{{%invoice}}',
            'inv_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-invoice_status_log_invsl_created_user_id',
            '{{%invoice_status_log}}',
            'invsl_created_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%invoice_status_log}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-invoice_status_log_invsl_created_user_id', '{{%invoice_status_log}}');
        $this->dropForeignKey('FK-invoice_status_log_invsl_invoice_id', '{{%invoice_status_log}}');
        $this->dropTable('{{%invoice_status_log}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
