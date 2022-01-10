<?php

use sales\model\clientChatChannel\entity\ClientChatChannel;
use yii\db\Migration;
use yii\helpers\Json;

/**
 * Class m210830_131021_update_chat_channel_settings_add_new_option
 */
class m210830_131021_update_chat_channel_settings_add_new_option extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $chatChannels = ClientChatChannel::find()->all();

        /** @var $channel ClientChatChannel */
        foreach ($chatChannels as $channel) {
            $settings = Json::decode($channel->ccc_settings);
            if (!isset($settings['system'])) {
                $settings['system'] = [];
            }

            $settings['system']['searchAndCacheFlightQuotesOnAcceptChat'] = true;

            $channel->ccc_settings = Json::encode($settings);
            $channel->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $chatChannels = ClientChatChannel::find()->all();

        /** @var $channel ClientChatChannel */
        foreach ($chatChannels as $channel) {
            $settings = Json::decode($channel->ccc_settings);

            if (isset($settings['system']['searchAndCacheFlightQuotesOnAcceptChat'])) {
                unset($settings['system']['searchAndCacheFlightQuotesOnAcceptChat']);
            }

            $channel->ccc_settings = Json::encode($settings);
            $channel->save();
        }
    }
}
