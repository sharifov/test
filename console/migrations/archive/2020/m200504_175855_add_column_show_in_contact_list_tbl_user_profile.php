<?php

use yii\db\Migration;

/**
 * Class m200504_175855_add_column_show_in_contact_list_tbl_user_profile
 */
class m200504_175855_add_column_show_in_contact_list_tbl_user_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_profile}}', 'up_show_in_contact_list', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_profile}}', 'up_show_in_contact_list');
    }
}
