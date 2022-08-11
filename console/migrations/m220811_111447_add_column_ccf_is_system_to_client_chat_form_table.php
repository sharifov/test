<?php

use src\model\clientChatForm\entity\ClientChatForm;
use yii\db\Migration;
use yii\db\Query;

/**
 * Class m220811_111447_add_column_ccf_is_system_to_client_chat_form_table
 */
class m220811_111447_add_column_ccf_is_system_to_client_chat_form_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%client_chat_form}}', 'ccf_is_system', $this->boolean()->defaultValue(false));
        (new Query())->createCommand()->update('{{%client_chat_form}}', [
            'ccf_is_system' => true
        ], [
            'ccf_key' => ClientChatForm::KEY_BOOKING_ID
        ])->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%client_chat_form}}', 'ccf_is_system');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220811_111447_add_column_ccf_is_system_to_client_chat_form_table cannot be reverted.\n";

        return false;
    }
    */
}
