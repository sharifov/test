<?php

use yii\db\Migration;

/**
 * Class m200622_134454_alter_tbl_user_profile_add_new_columns
 */
class m200622_134454_alter_tbl_user_profile_add_new_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_profile}}', 'up_rc_auth_token', $this->string(50));
        $this->addColumn('{{%user_profile}}', 'up_rc_user_id', $this->string(20));
        $this->addColumn('{{%user_profile}}', 'up_rc_user_password', $this->string(50));
        $this->addColumn('{{%user_profile}}', 'up_rc_token_expired', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_profile}}', 'up_rc_auth_token');
        $this->dropColumn('{{%user_profile}}', 'up_rc_user_id');
        $this->dropColumn('{{%user_profile}}', 'up_rc_user_password');
        $this->dropColumn('{{%user_profile}}', 'up_rc_token_expired');
    }
}
