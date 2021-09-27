<?php

use yii\db\Migration;

/**
 * Class m210926_092614_alter_tbl_call_alter_column_c_stir_status
 */
class m210926_092614_alter_tbl_call_alter_column_c_stir_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%call}}', 'c_stir_status', $this->string(2)->null());
        $this->alterColumn('{{%call_log}}', 'cl_stir_status', $this->string(2)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
