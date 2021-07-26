<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m210726_124419_create_tbl_product_quote_refund
 */
class m210726_124419_create_tbl_product_quote_refund extends Migration
{
    private string $tableName = '{{%product_quote_refund}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'pqr_id' => $this->primaryKey(),
            'pqr_order_refund_id' => $this->integer()->notNull(),
            'pqr_selling_price' => $this->decimal(8, 2),
            'pqr_penalty_amount' => $this->decimal(8, 2),
            'pqr_processing_fee_amount' => $this->decimal(8, 2),
            'pqr_refund_amount' => $this->decimal(8, 2),
            'pqr_status_id' => $this->tinyInteger(2),
            'pqr_client_currency' => $this->string(3)->defaultValue(null),
            'pqr_client_currency_rate' => $this->decimal(8, 2),
            'pqr_client_selling_price' => $this->decimal(8, 2),
            'pqr_client_refund_amount' => $this->decimal(8, 2),
            'pqr_created_user_id' => $this->integer(),
            'pqr_updated_user_id' => $this->integer(),
            'pqr_created_dt' => $this->dateTime(),
            'pqr_updated_dt' => $this->dateTime(),

        ], $tableOptions);

        $this->addForeignKey('FK-product_quote_refund-pqr_order_refund_id', $this->tableName, 'pqr_order_refund_id', '{{%order_refund}}', 'orr_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-product_quote_refund-pqr_client_currency', $this->tableName, 'pqr_client_currency', '{{%currency}}', 'cur_code', 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-product_quote_refund-pqr_created_user_id', $this->tableName, 'pqr_created_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-product_quote_refund-pqr_updated_user_id', $this->tableName, 'pqr_updated_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');

        $this->createIndex('IND-product_quote_refund-pqr_status_id', $this->tableName, 'pqr_status_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-product_quote_refund-pqr_order_refund_id', $this->tableName);

        $this->dropTable($this->tableName);
    }
}
