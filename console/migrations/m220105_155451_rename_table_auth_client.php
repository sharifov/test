<?php

use yii\db\Migration;

/**
 * Class m220105_155451_rename_table_auth_client
 */
class m220105_155451_rename_table_auth_client extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('FK-auth_client-ac_user_id', '{{%auth_client}}');
        $this->renameTable('auth_client', '{{%user_auth_client}}');
        $this->renameColumn('{{%user_auth_client}}', 'ac_id', 'uac_id');
        $this->renameColumn('{{%user_auth_client}}', 'ac_user_id', 'uac_user_id');
        $this->renameColumn('{{%user_auth_client}}', 'ac_source', 'uac_source');
        $this->renameColumn('{{%user_auth_client}}', 'ac_source_id', 'uac_source_id');
        $this->renameColumn('{{%user_auth_client}}', 'ac_email', 'uac_email');
        $this->renameColumn('{{%user_auth_client}}', 'ac_ip', 'uac_ip');
        $this->renameColumn('{{%user_auth_client}}', 'ac_useragent', 'uac_useragent');
        $this->renameColumn('{{%user_auth_client}}', 'ac_created_dt', 'uac_created_dt');
        $this->addForeignKey('FK-user_auth_client-uac_user_id', '{{%user_auth_client}}', 'uac_user_id', '{{%employees}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-user_auth_client-uac_user_id', '{{%user_auth_client}}');
        $this->renameTable('user_auth_client', '{{%auth_client}}');
        $this->renameColumn('{{%auth_client}}', 'uac_id', 'ac_id');
        $this->renameColumn('{{%auth_client}}', 'uac_user_id', 'ac_user_id');
        $this->renameColumn('{{%auth_client}}', 'uac_source', 'ac_source');
        $this->renameColumn('{{%auth_client}}', 'uac_source_id', 'ac_source_id');
        $this->renameColumn('{{%auth_client}}', 'uac_email', 'ac_email');
        $this->renameColumn('{{%auth_client}}', 'uac_ip', 'ac_ip');
        $this->renameColumn('{{%auth_client}}', 'uac_useragent', 'ac_useragent');
        $this->renameColumn('{{%auth_client}}', 'uac_created_dt', 'ac_created_dt');
        $this->addForeignKey('FK-auth_client-ac_user_id', '{{%auth_client}}', 'ac_user_id', '{{%employees}}', 'id', 'CASCADE', 'CASCADE');
    }
}
