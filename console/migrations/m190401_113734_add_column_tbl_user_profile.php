<?php

use yii\db\Migration;

/**
 * Class m190401_113734_add_column_tbl_user_profile
 */
class m190401_113734_add_column_tbl_user_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_profile}}', 'up_auto_redial', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_profile}}', 'up_auto_redial');
    }

}
