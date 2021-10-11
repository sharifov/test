<?php

use common\models\Setting;
use frontend\helpers\JsonHelper;
use yii\db\Migration;

/**
 * Class m211004_071559_alter_site_setting_call_spam_filter
 */
class m211004_071559_alter_site_setting_call_spam_filter extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $callbackToCallerSetting = Setting::find()->where(['s_key' => 'call_spam_filter'])->one();
        $setting = JsonHelper::decode($callbackToCallerSetting->s_value);
        $setting['spam_rate'] = $setting['rate'];
        $setting['trust_rate'] = 0.8;
        unset($setting['rate']);
        $callbackToCallerSetting->s_value = JsonHelper::encode($setting);
        $callbackToCallerSetting->save();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $callbackToCallerSetting = Setting::find()->where(['s_key' => 'call_spam_filter'])->one();
        $setting = JsonHelper::decode($callbackToCallerSetting->s_value);
        $setting['rate'] = $setting['spam_rate'];
        unset($setting['spam_rate'], $setting['trust_rate']);
        $callbackToCallerSetting->s_value = JsonHelper::encode($setting);
        $callbackToCallerSetting->save();
    }
}
