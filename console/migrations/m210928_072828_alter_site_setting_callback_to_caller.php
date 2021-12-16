<?php

use common\models\Setting;
use frontend\helpers\JsonHelper;
use yii\db\Migration;

/**
 * Class m210928_072828_alter_site_setting_callback_to_caller
 */
class m210928_072828_alter_site_setting_callback_to_caller extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $callbackToCallerSetting = Setting::find()->where(['s_key' => 'callback_to_caller'])->one();
        $setting = JsonHelper::decode($callbackToCallerSetting->s_value);
        $setting['excludeDepartmentKeys'] = [];
        $callbackToCallerSetting->s_value = JsonHelper::encode($setting);
        $callbackToCallerSetting->save();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $callbackToCallerSetting = Setting::find()->where(['s_key' => 'callback_to_caller'])->one();
        $setting = JsonHelper::decode($callbackToCallerSetting->s_value);
        if (isset($setting['excludeDepartmentKeys'])) {
            unset($setting['excludeDepartmentKeys']);
        }
        $callbackToCallerSetting->s_value = $setting;
        $callbackToCallerSetting->save();
    }
}
