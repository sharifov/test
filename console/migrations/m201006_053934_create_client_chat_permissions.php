<?php

use yii\db\Migration;

/**
 * Class m201006_053934_create_client_chat_permissions
 */
class m201006_053934_create_client_chat_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201006_053934_create_client_chat_permissions cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201006_053934_create_client_chat_permissions cannot be reverted.\n";

        return false;
    }
    */
}
