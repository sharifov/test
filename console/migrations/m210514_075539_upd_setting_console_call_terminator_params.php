<?php

use common\models\Setting;
use frontend\helpers\JsonHelper;
use yii\db\Migration;
use yii\helpers\ArrayHelper;

/**
 * Class m210514_075539_upd_setting_console_call_terminator_params
 */
class m210514_075539_upd_setting_console_call_terminator_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!$setting = Setting::findOne(['s_key' => 'console_call_terminator_params'])) {
            throw new RuntimeException('Setting "console_call_terminator_params" not found');
        }

        $value = JsonHelper::decode($setting->s_value);
        $value = ArrayHelper::merge($value, ['ivr_minutes' => 3]);
        $setting->s_value = JsonHelper::encode($value);

        if (!$setting->save()) {
            throw new RuntimeException(\src\helpers\ErrorsToStringHelper::extractFromModel($setting));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210514_075539_upd_setting_console_call_terminator_params cannot be reverted.\n";
    }
}
