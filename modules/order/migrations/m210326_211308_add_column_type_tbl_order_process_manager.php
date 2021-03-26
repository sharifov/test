<?php

namespace modules\order\migrations;

use yii\db\Migration;

/**
 * Class m210326_211308_add_column_type_tbl_order_process_manager
 */
class m210326_211308_add_column_type_tbl_order_process_manager extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_process_manager}}', 'opm_type', $this->tinyInteger());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order_process_manager}}', 'opm_type');
    }
}
