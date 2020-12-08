<?php

use yii\db\Migration;

/**
 * Class m201111_122407_add_columm_default_device_to_client_chat_channel_tbl
 */
class m201111_122407_add_columm_default_device_to_client_chat_channel_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%client_chat_channel}}', 'ccc_default_device', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%client_chat_channel}}', 'ccc_default_device');
    }
}
