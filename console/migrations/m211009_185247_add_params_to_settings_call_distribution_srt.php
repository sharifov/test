<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m211009_185247_add_params_to_settings_call_distribution_srt
 */
class m211009_185247_add_params_to_settings_call_distribution_srt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $model = Setting::findOne(['s_key' => 'call_distribution_sort']);
        if (!$model) {
            return;
        }
        $params = json_decode($model->s_value, true);
        $params['priority_level'] = 'DESC';
        $params['gross_profit'] = 'DESC';
        $model->s_value = json_encode($params);
        $model->save();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $model = Setting::findOne(['s_key' => 'call_distribution_sort']);
        if (!$model) {
            return;
        }
        $params = json_decode($model->s_value, true);
        if (array_key_exists('priority_level', $params)) {
            unset($params['priority_level']);
        }
        if (array_key_exists('gross_profit', $params)) {
            unset($params['gross_profit']);
        }
        $model->s_value = json_encode($params);
        $model->save();
    }
}
