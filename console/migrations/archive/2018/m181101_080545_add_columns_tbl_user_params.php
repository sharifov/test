<?php

use yii\db\Migration;

/**
 * Class m181101_080545_add_columns_tbl_user_params
 */
class m181101_080545_add_columns_tbl_user_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_params}}', 'up_inbox_show_limit_leads', $this->tinyInteger()->defaultValue(10));
        $this->addColumn('{{%user_params}}', 'up_default_take_limit_leads', $this->tinyInteger()->defaultValue(5));
        $this->addColumn('{{%user_params}}', 'up_min_percent_for_take_leads', $this->tinyInteger()->defaultValue(70));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_params}}', 'up_inbox_show_limit_leads');
        $this->dropColumn('{{%user_params}}', 'up_default_take_limit_leads');
        $this->dropColumn('{{%user_params}}', 'up_min_percent_for_take_leads');
    }
}
