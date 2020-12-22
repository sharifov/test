<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;
use yii\helpers\VarDumper;

/**
 * Class m201222_082326_add_chat_idle_timeout_settings
 */
class m201222_082326_add_chat_idle_timeout_settings extends Migration
{
    private string $categoryName = 'Client Chat';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName($this->categoryName);

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'client_chat_idle_timeout_offline_user',
                's_name' => 'Chat Idle timeout - offline users',
                's_type' => Setting::TYPE_INT,
                's_value' => 0,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );

        $chatInactiveMinute = Setting::find()->andWhere(['s_key' => 'client_chat_inactive_minutes'])->one();
        if ($chatInactiveMinute) {
            $chatInactiveMinute->s_key = 'client_chat_idle_timeout_online_user';
            $chatInactiveMinute->s_name = 'Chat Idle timeout - online users';
            if (!$chatInactiveMinute->save()) {
                VarDumper::dump($chatInactiveMinute->getErrors());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'client_chat_idle_timeout_offline_user',
        ]]);

        $chatInactiveMinute = Setting::find()->andWhere(['s_key' => 'client_chat_idle_timeout_online_user'])->one();
        if ($chatInactiveMinute) {
            $chatInactiveMinute->s_key = 'client_chat_inactive_minutes';
            $chatInactiveMinute->s_name = 'Client Chat inactive minutes';
            if (!$chatInactiveMinute->save()) {
                VarDumper::dump($chatInactiveMinute->getErrors());
            }
        }
    }
}
