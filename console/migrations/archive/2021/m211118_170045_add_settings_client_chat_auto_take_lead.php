<?php

use common\models\Lead;
use common\models\Setting;
use common\models\SettingCategory;
use src\model\clientChatChannel\entity\ClientChatChannel;
use yii\db\Migration;
use yii\helpers\Json;

/**
 * Class m211118_170045_add_settings_client_chat_auto_take_lead
 */
class m211118_170045_add_settings_client_chat_auto_take_lead extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Client Chat');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'client_chat_lead_auto_take',
                's_name' => ' Client chat. Lead auto take',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    'on_chat_accept' => false,
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );

        $chatChannels = ClientChatChannel::find()->all();

        /** @var $channel ClientChatChannel */
        foreach ($chatChannels as $channel) {
            $settings = Json::decode($channel->ccc_settings);
            if (isset($settings['leadAutoTake'])) {
                continue;
            }
            $settings['leadAutoTake'] = [
                'onChatAccept' => null,
                'availableStatuses' => [
                    Lead::STATUS_LIST[Lead::STATUS_PENDING],
                    Lead::STATUS_LIST[Lead::STATUS_NEW],
                ],
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
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'client_chat_lead_auto_take',
        ]]);

        $chatChannels = ClientChatChannel::find()->all();

        /** @var $channel ClientChatChannel */
        foreach ($chatChannels as $channel) {
            $settings = Json::decode($channel->ccc_settings);
            if (!isset($settings['leadAutoTake'])) {
                continue;
            }
            unset($settings['leadAutoTake']);
            $channel->ccc_settings = Json::encode($settings);
            $channel->save();
        }
    }
}
