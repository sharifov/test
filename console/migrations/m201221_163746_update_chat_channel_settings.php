<?php

use sales\model\clientChatChannel\entity\ClientChatChannel;
use yii\db\Migration;
use yii\helpers\Json;

/**
 * Class m201221_163746_update_chat_channel_settings
 */
class m201221_163746_update_chat_channel_settings extends Migration
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

            $settings['system']['userAccessDistribution'] = [
                'repeatDelaySeconds' => 0,
                'userLimit' => 0,
                'sortParameters' => [
                    'pastAcceptedChatsNumber' => [
                        'pastMinutes' => 180,
                        'sortPriority' => 0
                    ]
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

            if (isset($settings['system']['userAccessDistribution'])) {
                unset($settings['system']['userAccessDistribution']);
            }

            $channel->ccc_settings = Json::encode($settings);
            $channel->save();
        }
    }
}
