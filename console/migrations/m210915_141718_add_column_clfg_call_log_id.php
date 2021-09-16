<?php

use yii\db\Migration;

/**
 * Class m210915_141718_add_column_clfg_call_log_id
 */
class m210915_141718_add_column_clfg_call_log_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%call_log_filter_guard}}', 'clfg_call_log_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%call_log_filter_guard}}', 'clfg_call_log_id');
    }
}
