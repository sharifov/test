<?php

use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatChannel\entity\ClientChatChannelDefaultSettings;
use yii\db\Migration;
use yii\helpers\Json;

/**
 * Class m201103_151444_add_new_default_channel_settings_to_existing
 */
class m201103_151444_add_new_default_channel_settings_to_existing extends Migration
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
            if (isset($settings['registration']['enabled'])) {
                $registrationEnabledValue = $settings['registration']['enabled'];
                unset($settings['registration']['enabled']);
                $settings['registration']['formFieldsEnabled'] = $registrationEnabledValue;
            } else {
                $registrationEnabledValue = ClientChatChannelDefaultSettings::getAll()['registration']['formFieldsEnabled'];
                $settings['registration']['formFieldsEnabled'] = $registrationEnabledValue;
            }

            $settings['system']['displayOrder'] = 0;

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
