<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m210727_080524_create_tbl_product_quote_object_refund
 */
class m210727_080524_create_tbl_product_quote_object_refund extends Migration
{
    private string $tableName = '{{%product_quote_object_refund}}';

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
            'pqor_id' => $this->primaryKey(),
            'pqor_product_quote_refund_id' => $this->integer()->notNull(),
            'pqor_selling_price' => $this->decimal(8, 2),
            'pqor_penalty_amount' => $this->decimal(8, 2),
            'pqor_processing_fee_amount' => $this->decimal(8, 2),
            'pqor_refund_amount' => $this->decimal(8, 2),
            'pqor_status_id' => $this->tinyInteger(2),
            'pqor_client_currency' => $this->string(3)->defaultValue(null),
            'pqor_client_currency_rate' => $this->decimal(8, 2),
            'pqor_client_selling_price' => $this->decimal(8, 2),
            'pqor_client_refund_amount' => $this->decimal(8, 2),
            'pqor_created_user_id' => $this->integer(),
            'pqor_updated_user_id' => $this->integer(),
            'pqor_created_dt' => $this->dateTime(),
            'pqor_updated_dt' => $this->dateTime()
        ], $tableOptions);

        $this->addForeignKey('FK-product_quote_object_refund-pqor_product_quote_refund_id', $this->tableName, 'pqor_product_quote_refund_id', '{{%product_quote_refund}}', 'pqr_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-product_quote_object_refund-pqor_created_user_id', $this->tableName, 'pqor_created_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-product_quote_object_refund-pqor_updated_user_id', $this->tableName, 'pqor_updated_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-product_quote_object_refund-pqor_client_currency', $this->tableName, 'pqor_client_currency', '{{%currency}}', 'cur_code', 'SET NULL', 'CASCADE');

        $this->createIndex('IND-product_quote_object_refund-pqor_status_id', $this->tableName, 'pqor_status_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-product_quote_object_refund-pqor_product_quote_refund_id', $this->tableName);
        $this->dropForeignKey('FK-product_quote_object_refund-pqor_created_user_id', $this->tableName);
        $this->dropForeignKey('FK-product_quote_object_refund-pqor_updated_user_id', $this->tableName);
        $this->dropForeignKey('FK-product_quote_object_refund-pqor_client_currency', $this->tableName);

        $this->dropTable($this->tableName);
    }
}
