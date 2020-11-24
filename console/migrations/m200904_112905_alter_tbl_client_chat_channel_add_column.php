<?php

use yii\db\Migration;

/**
 * Class m200904_112905_alter_tbl_client_chat_channel_add_column
 */
class m200904_112905_alter_tbl_client_chat_channel_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%client_chat_channel}}', 'ccc_default', $this->tinyInteger(1)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%client_chat_channel}}', 'ccc_default');
    }
}
