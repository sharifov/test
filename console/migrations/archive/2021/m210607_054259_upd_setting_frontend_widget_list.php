<?php

use common\models\Setting;
use frontend\helpers\JsonHelper;
use yii\db\Migration;
use yii\helpers\ArrayHelper;

/**
 * Class m210607_054259_upd_setting_frontend_widget_list
 */
class m210607_054259_upd_setting_frontend_widget_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($frontendWidgetList = Setting::findOne(['s_key' => 'frontend_widget_list'])) {
            $value = JsonHelper::decode($frontendWidgetList->s_value);
            ArrayHelper::setValue(
                $value,
                'louassist.className',
                'frontend\widgets\frontendWidgetList\louassist\LouAssistWidget'
            );

            if ($scriptId = ArrayHelper::getValue($value, 'louassist.id')) {
                ArrayHelper::setValue(
                    $value,
                    'louassist.params.scriptId',
                    $scriptId
                );
                unset($value['louassist']['id']);
            }

            $frontendWidgetList->s_value = JsonHelper::encode($value);
            $frontendWidgetList->save(false);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($frontendWidgetList = Setting::findOne(['s_key' => 'frontend_widget_list'])) {
            $value = JsonHelper::decode($frontendWidgetList->s_value);

            if (ArrayHelper::getValue($value, 'louassist.className')) {
                unset($value['louassist']['className']);
            }

            if ($scriptId = ArrayHelper::getValue($value, 'louassist.params.scriptId')) {
                unset($value['louassist']['params']['scriptId']);
                ArrayHelper::setValue(
                    $value,
                    'louassist.id',
                    $scriptId
                );
            }

            $frontendWidgetList->s_value = JsonHelper::encode($value);
            $frontendWidgetList->save(false);
        }
    }
}
