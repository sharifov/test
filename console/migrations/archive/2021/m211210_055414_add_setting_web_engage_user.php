<?php

use common\models\Setting;
use frontend\helpers\JsonHelper;
use modules\webEngage\settings\WebEngageDictionary;
use yii\db\Migration;
use yii\helpers\Console;

/**
 * Class m211210_055414_add_setting_web_engage_user
 */
class m211210_055414_add_setting_web_engage_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%setting}}', 's_value', $this->string(2000));

        if ($webEngage = Setting::findOne(['s_key' => 'web_engage'])) {
            $value = JsonHelper::decode($webEngage->s_value);
            foreach (WebEngageDictionary::EVENT_LIST as $eventName) {
                $value[$eventName]['isSendUserCreateRequest'] = false;
            }
            $value['sourceCIds'] = [];

            $webEngage->s_value = JsonHelper::encode($value);

            if (!$webEngage->save()) {
                \Yii::error([
                    'message' => 'Setting web_engage not saved',
                    'model' => $webEngage->getAttributes(),
                    'errors' => $webEngage->getErrors(),
                ], 'm211208_124559_add_setting_web_engage_create_lead');
                echo Console::renderColoredString('%r --- SafeUp Error : %pSetting web_engage not saved. See logs.%n'), PHP_EOL;
            }
        }

        Yii::$app->db->createCommand()->upsert('{{%client_data_key}}', [
            'cdk_key' => 'is_sending_to_web_engage',
            'cdk_name' => 'Is Sending To WebEngage',
            'cdk_enable' => true,
            'cdk_is_system' => true,
            'cdk_description' => 'Is Sending To WebEngage',
        ])->execute();

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($webEngage = Setting::findOne(['s_key' => 'web_engage'])) {
            $value = JsonHelper::decode($webEngage->s_value);
            foreach (WebEngageDictionary::EVENT_LIST as $eventName) {
                unset($value[$eventName]['isSendUserCreateRequest'], $value[$eventName]['sourceCIds']);
            }

            $webEngage->s_value = JsonHelper::encode($value);

            if (!$webEngage->save()) {
                echo Console::renderColoredString('%r --- SafeDown Error : %pSetting web_engage not saved. See logs.%n'), PHP_EOL;
            }
        }

        $this->delete('{{%client_data_key}}', ['IN', 'cdk_key', [
            'is_sending_to_web_engage'
        ]]);
    }
}
