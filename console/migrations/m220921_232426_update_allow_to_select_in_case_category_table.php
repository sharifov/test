<?php

use yii\db\Migration;

/**
 * Class m220921_232426_update_allow_to_select_in_case_category_table
 */
class m220921_232426_update_allow_to_select_in_case_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('{{%case_category}}', [
            'cc_allow_to_select' => 1,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
