<?php

use yii\db\Migration;

/**
 * Class m201111_060618_add_new_default_option_to_client_chat_channel_settings
 */
class m201111_060618_add_new_default_option_to_client_chat_channel_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
//        $chatChannels = \sales\model\clientChatChannel\entity\ClientChatChannel::find()->all();
//
//        /** @var $channel ClientChatChannel */
//        foreach ($chatChannels as $channel) {
//            $settings = \yii\helpers\Json::decode($channel->ccc_settings);
//            if (!isset($settings['system']['allowRealtime'])) {
//                $settings['system']['allowRealtime'] = true;
//            }
//
//            $channel->ccc_settings = \yii\helpers\Json::encode($settings);
//            $channel->save();
//        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201111_060618_add_new_default_option_to_client_chat_channel_settings cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201111_060618_add_new_default_option_to_client_chat_channel_settings cannot be reverted.\n";

        return false;
    }
    */
}
