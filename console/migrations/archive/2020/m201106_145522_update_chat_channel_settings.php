<?php

use sales\model\clientChatChannel\entity\ClientChatChannelDefaultSettings;
use yii\db\Migration;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use yii\helpers\Json;

/**
 * Class m201106_145522_update_chat_channel_settings
 */
class m201106_145522_update_chat_channel_settings extends Migration
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
            if (isset($settings['system']['displayOrder'])) {
                unset($settings['system']['displayOrder']);
            }

            if (!isset($settings['displayOrder'])) {
                $registrationEnabledValue = ClientChatChannelDefaultSettings::getAll()['displayOrder'];
                $settings['displayOrder'] = $registrationEnabledValue;
            }

            $channel->ccc_settings = Json::encode($settings);
            $channel->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
