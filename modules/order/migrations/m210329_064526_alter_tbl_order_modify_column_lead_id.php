<?php

namespace modules\order\migrations;

use yii\db\Migration;

/**
 * Class m210329_064526_alter_tbl_order_modify_column_lead_id
 */
class m210329_064526_alter_tbl_order_modify_column_lead_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%order}}', 'or_lead_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%order}}', 'or_lead_id', $this->integer()->notNull());
    }
}
