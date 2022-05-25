<?php

use yii\db\Migration;

/**
 * Class m220524_152224_add_index_user_failed_login_ufl_ip_ufl_created_dt
 */
class m220524_152224_add_index_user_failed_login_ufl_ip_ufl_created_dt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-user_failed_login-ufl_ip-ufl_created_dt', '{{%user_failed_login}}', [
            'ufl_ip',
            'ufl_created_dt'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-user_failed_login-ufl_ip-ufl_created_dt', '{{%user_failed_login}}');
    }
}
