<?php

namespace modules\order\migrations;

use yii\db\Migration;

/**
 * Class m210409_100731_create_tbl_order_email
 */
class m210409_100731_create_tbl_order_email extends Migration
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

        $this->createTable('{{%order_email}}', [
            'oe_id' => $this->primaryKey(),
            'oe_order_id' => $this->integer(),
            'oe_email_id' => $this->integer(),
            'oe_created_dt' => $this->dateTime()
        ], $tableOptions);

        $this->addForeignKey('FK-order_email-oe_order_id', '{{%order_email}}', 'oe_order_id', '{{%order}}', 'or_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-order_email-oe_email_id', '{{%order_email}}', 'oe_email_id', '{{%email}}', 'e_id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-order_email-oe_order_id', '{{%order_email}}');
        $this->dropForeignKey('FK-order_email-oe_email_id', '{{%order_email}}');
        $this->dropTable('{{%order_email}}');
    }
}
