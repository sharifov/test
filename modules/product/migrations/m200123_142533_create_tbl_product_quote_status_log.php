<?php

namespace modules\product\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200123_142533_create_tbl_product_quote_status_log
 */
class m200123_142533_create_tbl_product_quote_status_log extends Migration
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

        $this->createTable('{{%product_quote_status_log}}', [
            'pqsl_id' => $this->primaryKey(),
            'pqsl_product_quote_id' => $this->integer()->notNull(),
            'pqsl_start_status_id' => $this->tinyInteger(),
            'pqsl_end_status_id' => $this->tinyInteger()->notNull(),
            'pqsl_start_dt' => $this->dateTime()->notNull(),
            'pqsl_end_dt' => $this->dateTime(),
            'pqsl_duration' => $this->integer(),
            'pqsl_description' => $this->string(255),
            'pqsl_owner_user_id' => $this->integer(),
            'pqsl_created_user_id' => $this->integer(),

        ], $tableOptions);

        $this->addForeignKey(
            'FK-product_quote_status_log_pqsl_product_quote_id',
            '{{%product_quote_status_log}}',
            'pqsl_product_quote_id',
            '{{%product_quote}}',
            'pq_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-product_quote_status_log_pqsl_owner_user_id',
            '{{%product_quote_status_log}}',
            'pqsl_owner_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-product_quote_status_log_pqsl_created_user_id',
            '{{%product_quote_status_log}}',
            'pqsl_created_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%product_quote_status_log}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-product_quote_status_log_pqsl_created_user_id', '{{%product_quote_status_log}}');
        $this->dropForeignKey('FK-product_quote_status_log_pqsl_owner_user_id', '{{%product_quote_status_log}}');
        $this->dropForeignKey('FK-product_quote_status_log_pqsl_product_quote_id', '{{%product_quote_status_log}}');
        $this->dropTable('{{%product_quote_status_log}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
