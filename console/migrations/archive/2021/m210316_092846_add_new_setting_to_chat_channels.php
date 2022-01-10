<?php

use sales\model\clientChatChannel\entity\ClientChatChannel;
use yii\db\Migration;

/**
 * Class m210316_092846_add_new_setting_to_chat_channels
 */
class m210316_092846_add_new_setting_to_chat_channels extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $channels = ClientChatChannel::find()->all();

        foreach ($channels as $channel) {
            $settings = $channel->settings;

            if (!isset($settings['system'])) {
                $settings['system'] = [];
            }

            if (!isset($settings['system']['autoCloseRoom'])) {
                $settings['system']['autoCloseRoom'] = true;
            }

            $channel->ccc_settings = \yii\helpers\Json::encode($settings);

            if (!$channel->save()) {
                throw new RuntimeException($channel->getErrorSummary(true)[0]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $channels = ClientChatChannel::find()->all();

        foreach ($channels as $channel) {
            $settings = $channel->settings;

            if (isset($settings['system']['autoCloseRoom'])) {
                unset($settings['system']['autoCloseRoom']);
            }

            $channel->ccc_settings = \yii\helpers\Json::encode($settings);

            if (!$channel->save()) {
                throw new RuntimeException($channel->getErrorSummary(true)[0]);
            }
        }
    }
}
