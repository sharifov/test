<?php

namespace modules\order\migrations;

use yii\db\Migration;

/**
 * Class m210407_082505_create_tbl_order_contact
 */
class m210407_082505_create_tbl_order_contact extends Migration
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

        $this->createTable('{{%order_contact}}', [
            'oc_id' => $this->primaryKey(),
            'oc_order_id' => $this->integer(),
            'oc_first_name' => $this->string(50),
            'oc_last_name' => $this->string(50),
            'oc_middle_name' => $this->string(50),
            'oc_email' => $this->string(100),
            'oc_phone_number' => $this->string(20),
            'oc_created_dt' => $this->dateTime(),
            'oc_updated_dt' => $this->dateTime()
        ], $tableOptions);

        $this->addForeignKey('FK-order_contact-oc_order_id', '{{%order_contact}}', 'oc_order_id', '{{%order}}', 'or_id', 'CASCADE', 'CASCADE');
        $this->createIndex('IND-order_contact-oc_email', '{{%order_contact}}', 'oc_email');
        $this->createIndex('IND-order_contact-oc_phone_number', '{{%order_contact}}', 'oc_phone_number');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-order_contact-oc_order_id', '{{%order_contact}}');
        $this->dropTable('{{%order_contact}}');
    }
}
