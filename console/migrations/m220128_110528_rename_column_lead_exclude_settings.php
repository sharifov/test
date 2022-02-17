<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m220128_110528_rename_column_lead_exclude_settings
 */
class m220128_110528_rename_column_lead_exclude_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $setting = Setting::find()->andWhere(['s_key' => 'redial_lead_exclude_attributes'])->one();

        if ($setting) {
            $value = json_decode($setting->s_value, true);
            unset($value['hasFlightDetails']);
            unset($value['noFlightDetails']);
            $value['noFlightDetails'] = false;
            $setting->s_value = json_encode($value);
            $setting->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
