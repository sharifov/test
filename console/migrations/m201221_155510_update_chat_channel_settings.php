<?php

use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatChannel\entity\ClientChatChannelDefaultSettings;
use yii\db\Migration;
use yii\helpers\Json;

/**
 * Class m201221_155510_update_chat_channel_settings
 */
class m201221_155510_update_chat_channel_settings extends Migration
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
            $settings['system']['repeatDelaySeconds'] = 0;
            $settings['system']['userLimit'] = 0;
            $settings['system']['sortParameters'] = [
                'pastAcceptedChatsNumber' => [
                    'pastMinutes' => 180,
                    'sortPriority' => 0
                ]
            ];
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

            if (isset($settings['system']['repeatDelaySeconds'])) {
                unset($settings['system']['repeatDelaySeconds']);
            }
            if (isset($settings['system']['userLimit'])) {
                unset($settings['system']['userLimit']);
            }
            if (isset($settings['system']['sortParameters'])) {
                unset($settings['system']['sortParameters']);
            }
            $channel->ccc_settings = Json::encode($settings);
            $channel->save();
        }
    }
}
