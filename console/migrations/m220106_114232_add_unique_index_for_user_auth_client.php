<?php

use yii\db\Migration;

/**
 * Class m220106_114232_add_unique_index_for_user_auth_client
 */
class m220106_114232_add_unique_index_for_user_auth_client extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('UNQ-user_auth_client', '{{%user_auth_client}}', ['uac_user_id', 'uac_source', 'uac_source_id'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('UNQ-user_auth_client', '{{%user_auth_client}}');
    }
}
