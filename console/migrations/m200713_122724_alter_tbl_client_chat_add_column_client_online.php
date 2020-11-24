<?php

use yii\db\Migration;

/**
 * Class m200713_122724_alter_tbl_client_chat_add_column_client_online
 */
class m200713_122724_alter_tbl_client_chat_add_column_client_online extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%client_chat}}', 'cch_client_online', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%client_chat}}', 'cch_client_online');
    }
}
