<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m210407_142601_alter_tbl_product_holder_add_columns
 */
class m210407_142601_alter_tbl_product_holder_add_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_holder}}', 'ph_middle_name', $this->string(50)->after('ph_last_name'));
        $this->addColumn('{{%product_holder}}', 'ph_updated_dt', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_holder}}', 'ph_middle_name');
        $this->dropColumn('{{%product_holder}}', 'ph_updated_dt');
    }
}
