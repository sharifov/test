<?php

use src\model\clientChatChannel\entity\ClientChatChannel;
use yii\db\Migration;
use yii\helpers\Json;

/**
 * Class m211202_072824_alter_chat_channel_settings
 */
class m211202_072824_alter_chat_channel_settings extends Migration
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

            $pastAcceptedChatsNumber = $settings['system']['userAccessDistribution']['sortParameters']['pastAcceptedChatsNumber'] ?? null;
            if ($pastAcceptedChatsNumber) {
                $pastAcceptedChatsNumber['enabled'] = true;
                $settings['system']['userAccessDistribution']['sortParameters']['pastAcceptedChatsNumber'] = $pastAcceptedChatsNumber;
            }

            $settings['system']['userAccessDistribution']['sortParameters']['skillLevel'] = [
                'sortDirection' => 'ASC',
                'sortPriority' => 0,
                'enabled' => true
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

            if (isset($settings['system']['userAccessDistribution']['sortParameters']['pastAcceptedChatsNumber']['enabled'])) {
                unset($settings['system']['userAccessDistribution']['sortParameters']['pastAcceptedChatsNumber']['enabled']);
            }

            if (isset($settings['system']['userAccessDistribution']['sortParameters']['skillLevel'])) {
                unset($settings['system']['userAccessDistribution']['sortParameters']['skillLevel']);
            }

            $channel->ccc_settings = Json::encode($settings);
            $channel->save();
        }
    }
}
