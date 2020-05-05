<?php

use sales\entities\cases\Cases;
use yii\db\Migration;

/**
 * Class m200304_131614_change_default_value_need_action_tbl_cases
 */
class m200304_131614_change_default_value_need_action_tbl_cases extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%cases}}', 'cs_need_action', $this->boolean()->defaultValue(null)->null());
        Cases::updateAll(['cs_need_action' => null], 'cs_need_action = 0');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
