<?php

use yii\db\Migration;

/**
 * Class m210407_073444_create_tbl_lead_order
 */
class m210407_073444_create_tbl_lead_order extends Migration
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

        $this->createTable('{{%lead_order}}', [
            'lo_order_id' => $this->integer(),
            'lo_lead_id' => $this->integer(),
            'lo_create_dt' => $this->dateTime(),
            'lo_created_user_id' => $this->integer()
        ], $tableOptions);

        $this->addPrimaryKey(
            'PK-lo_order_id-co_order_id-lo_lead_id',
            '{{%lead_order}}',
            [
                'lo_order_id',
                'lo_lead_id'
            ]
        );

        $this->addForeignKey('PK-lead_order-lo_order_id', '{{%lead_order}}', 'lo_order_id', '{{%order}}', 'or_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('PK-lead_order-lo_lead_id', '{{%lead_order}}', 'lo_lead_id', '{{%leads}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('PK-lead_order-lo_created_user_id', '{{%lead_order}}', 'lo_created_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('PK-lead_order-lo_order_id', '{{%lead_order}}');
        $this->dropForeignKey('PK-lead_order-lo_lead_id', '{{%lead_order}}');
        $this->dropForeignKey('PK-lead_order-lo_created_user_id', '{{%lead_order}}');
        $this->dropTable('{{%lead_order}}');
    }
}
