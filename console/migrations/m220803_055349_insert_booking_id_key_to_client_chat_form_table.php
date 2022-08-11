<?php

use src\model\clientChatForm\entity\ClientChatForm;
use yii\db\Migration;

/**
 * Class m220803_055349_insert_booking_id_key_to_client_chat_form_table
 */
class m220803_055349_insert_booking_id_key_to_client_chat_form_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (ClientChatForm::find()->where(['ccf_key' =>  ClientChatForm::KEY_BOOKING_ID])->exists()) {
            return;
        }
        $this->insert('{{%client_chat_form}}', [
            'ccf_key'             => ClientChatForm::KEY_BOOKING_ID,
            'ccf_name'            => 'Booking id',
            'ccf_enabled'         => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (!ClientChatForm::find()->where(['ccf_key' =>  ClientChatForm::KEY_BOOKING_ID])->exists()) {
            return;
        }

        $this->delete('{{%client_chat_form}}', ['IN', 'ccf_key', [
            ClientChatForm::KEY_BOOKING_ID
        ]]);
    }
}
