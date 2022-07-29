<?php

use yii\db\Migration;

/**
 * Class m220725_110143_add_columns_tbl_user_params
 */
class m220725_110143_add_columns_tbl_user_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_params}}', 'up_business_inbox_show_limit_leads', $this->tinyInteger()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_params}}', 'up_business_inbox_show_limit_leads');
    }
}
