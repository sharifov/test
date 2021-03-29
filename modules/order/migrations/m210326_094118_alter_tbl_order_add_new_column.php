<?php

namespace modules\order\migrations;

use yii\db\Migration;

/**
 * Class m210326_094118_alter_tbl_order_add_new_column
 */
class m210326_094118_alter_tbl_order_add_new_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'or_request_id', $this->integer());
        $this->addColumn('{{%order}}', 'or_project_id', $this->integer());
        $this->addColumn('{{%order}}', 'or_type_id', $this->tinyInteger(1));

        $this->addForeignKey('FK-order-or_project_id', '{{%order}}', 'or_project_id', '{{%projects}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-order-or_request_id', '{{%order}}', 'or_request_id', '{{%order_request}}', 'orr_id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-order-or_project_id', '{{%order}}');
        $this->dropForeignKey('FK-order-or_request_id', '{{%order}}');
        $this->dropColumn('{{%order}}', 'or_project_id');
        $this->dropColumn('{{%order}}', 'or_type_id');
        $this->dropColumn('{{%order}}', 'or_request_id');
    }
}
