<?php

namespace modules\order\migrations;

use yii\db\Migration;

/**
 * Class m210416_050700_add_column_to_tbl_order_contact
 */
class m210416_050700_add_column_to_tbl_order_contact extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_contact}}', 'oc_client_id', $this->integer());
        $this->addForeignKey('FK-order_contact_oc_client_id', '{{%order_contact}}', 'oc_client_id', '{{%clients}}', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-order_contact_oc_client_id', '{{%order_contact}}');
        $this->dropColumn('{{%order_contact}}', 'oc_client_id');
    }
}
