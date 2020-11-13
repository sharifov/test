<?php

use yii\db\Migration;

/**
 * Class m200911_082917_add_column_settings_tbl_chat_channel
 */
class m200911_082917_add_column_settings_tbl_chat_channel extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%client_chat_channel}}', 'ccc_frontend_name', $this->string(100)->notNull());
        $this->addColumn('{{%client_chat_channel}}', 'ccc_frontend_enabled', $this->boolean()->defaultValue(false));
        $this->addColumn('{{%client_chat_channel}}', 'ccc_settings', $this->json());

        $this->createIndex('IND-client_chat_channel-ccc_frontend_enabled','{{%client_chat_channel}}', 'ccc_frontend_enabled');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%client_chat_channel}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-client_chat_channel-ccc_frontend_enabled','{{%client_chat_channel}}');

        $this->dropColumn('{{%client_chat_channel}}', 'ccc_settings');
        $this->dropColumn('{{%client_chat_channel}}', 'ccc_frontend_enabled');
        $this->dropColumn('{{%client_chat_channel}}', 'ccc_frontend_name');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%client_chat_channel}}');
    }
}
