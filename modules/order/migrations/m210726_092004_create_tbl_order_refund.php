<?php

namespace modules\order\migrations;

use yii\db\Migration;

/**
 * Class m210726_092004_create_tbl_order_refund
 */
class m210726_092004_create_tbl_order_refund extends Migration
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

        $this->createTable('{{%order_refund}}', [
            'orr_id' => $this->primaryKey(),
            'orr_uid' => $this->string(15)->notNull(),
            'orr_order_id' => $this->integer()->notNull(),
            'orr_selling_price' => $this->decimal(8, 2),
            'orr_penalty_amount' => $this->decimal(8, 2),
            'orr_processing_fee_amount' => $this->decimal(8, 2),
            'orr_charge_amount' => $this->decimal(8, 2),
            'orr_refund_amount' => $this->decimal(8, 2),
            'orr_client_status_id' => $this->tinyInteger(2),
            'orr_status_id' => $this->tinyInteger(2),
            'orr_client_currency' => $this->string(3),
            'orr_client_currency_rate' => $this->decimal(8, 2),
            'orr_client_selling_price' => $this->decimal(8, 2),
            'orr_client_charge_amount' => $this->decimal(8, 2),
            'orr_client_refund_amount' => $this->decimal(8, 2),
            'orr_description' => $this->text(),
            'orr_expiration_dt' => $this->dateTime(),
            'orr_created_user_id' => $this->integer(),
            'orr_updated_user_id' => $this->integer(),
            'orr_created_dt' => $this->dateTime(),
            'orr_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey('FK-order_refund-orr_order_id', '{{%order_refund}}', 'orr_order_id', '{{%order}}', 'or_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-order_refund-orr_client_currency', '{{%order_refund}}', 'orr_client_currency', '{{%currency}}', 'cur_code', 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-order_refund-orr_created_user_id', '{{%order_refund}}', 'orr_created_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-order_refund-orr_updated_user_id', '{{%order_refund}}', 'orr_updated_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');

        $this->createIndex('IND-order_refund-order_id', '{{%order_refund}}', 'orr_uid');
        $this->createIndex('IND-order_refund-orr_client_status_id', '{{%order_refund}}', 'orr_client_status_id');
        $this->createIndex('IND-order_refund-orr_status_id', '{{%order_refund}}', 'orr_status_id');
        $this->createIndex('IND-order_refund-orr_expiration_dt', '{{%order_refund}}', 'orr_expiration_dt');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-order_refund-orr_order_id', '{{%order_refund}}');
        $this->dropForeignKey('FK-order_refund-orr_client_currency', '{{%order_refund}}');
        $this->dropForeignKey('FK-order_refund-orr_created_user_id', '{{%order_refund}}');
        $this->dropForeignKey('FK-order_refund-orr_updated_user_id', '{{%order_refund}}');

        $this->dropTable('{{%order_refund}}');
    }
}
