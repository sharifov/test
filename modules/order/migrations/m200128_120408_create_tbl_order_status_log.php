<?php

namespace modules\order\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200128_120408_create_tbl_order_status_log
 */
class m200128_120408_create_tbl_order_status_log extends Migration
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

        $this->createTable('{{%order_status_log}}', [
            'orsl_id' => $this->primaryKey(),
            'orsl_order_id' => $this->integer()->notNull(),
            'orsl_start_status_id' => $this->tinyInteger(),
            'orsl_end_status_id' => $this->tinyInteger()->notNull(),
            'orsl_start_dt' => $this->dateTime()->notNull(),
            'orsl_end_dt' => $this->dateTime(),
            'orsl_duration' => $this->integer(),
            'orsl_description' => $this->string(255),
            'orsl_action_id' => $this->tinyInteger(),
            'orsl_owner_user_id' => $this->integer(),
            'orsl_created_user_id' => $this->integer(),

        ], $tableOptions);

        $this->addForeignKey(
            'FK-order_status_log_orsl_order_id',
            '{{%order_status_log}}',
            'orsl_order_id',
            '{{%order}}',
            'or_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-order_status_log_orsl_owner_user_id',
            '{{%order_status_log}}',
            'orsl_owner_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-order_status_log_orsl_created_user_id',
            '{{%order_status_log}}',
            'orsl_created_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%order_status_log}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-order_status_log_orsl_created_user_id', '{{%order_status_log}}');
        $this->dropForeignKey('FK-order_status_log_orsl_owner_user_id', '{{%order_status_log}}');
        $this->dropForeignKey('FK-order_status_log_orsl_order_id', '{{%order_status_log}}');
        $this->dropTable('{{%order_status_log}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
