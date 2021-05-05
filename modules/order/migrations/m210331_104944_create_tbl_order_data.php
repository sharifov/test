<?php

namespace modules\order\migrations;

use yii\db\Migration;

/**
 * Class m210331_104944_create_tbl_order_data
 */
class m210331_104944_create_tbl_order_data extends Migration
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

        $this->createTable('{{%order_data}}', [
            'od_id' => $this->primaryKey(),
            'od_order_id' => $this->integer()->notNull(),
            'od_display_uid' => $this->string(10),
            'od_source_cid' => $this->string(10),
            'od_created_by' => $this->integer(),
            'od_updated_by' => $this->integer(),
            'od_created_dt' => $this->dateTime(),
            'od_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey('FK-order_data-od_order_id', '{{%order_data}}', 'od_order_id', '{{%order}}', 'or_id', 'CASCADE', 'CASCADE');

        $this->addForeignKey('FK-order_data-od_created_by', '{{%order_data}}', 'od_created_by', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-order_data-od_updated_by', '{{%order_data}}', 'od_updated_by', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-order_data-od_order_id', '{{%order_data}}');
        $this->dropForeignKey('FK-order_data-od_created_by', '{{%order_data}}');
        $this->dropForeignKey('FK-order_data-od_updated_by', '{{%order_data}}');

        $this->dropTable('{{%order_data}}');
    }
}
