<?php

use yii\db\Migration;

/**
 * Class m220329_073203_fix_price_research_tool_justfly_name_settings
 */
class m220329_073203_fix_price_research_tool_justfly_name_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $setting = \common\models\Setting::find()->where(['s_key' => 'price_research_links'])->one();
            if (empty($setting)) {
                return;
            }
            $settingValue    = json_decode($setting->s_value, true);
            $justflySettings = \yii\helpers\ArrayHelper::getValue($settingValue, 'justify_com');
            if (empty($justflySettings)) {
                return;
            }
            $justflySettings['name'] = 'Justfly.com';
            unset($settingValue['justify_com']);
            $settingValue['justfly_com'] = $justflySettings;
            $setting->s_value            = json_encode($settingValue, true);
            $setting->save();
            if (Yii::$app->cache) {
                Yii::$app->cache->flush();
            }

            Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
        } catch (Throwable $throwable) {
            Yii::error(
                $throwable,
                'm220329_073203_fix_price_research_tool_justfly_name_settings:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $setting = \common\models\Setting::find()->where(['s_key' => 'price_research_links'])->one();
            if (empty($setting)) {
                return;
            }
            $settingValue    = json_decode($setting->s_value, true);
            $justflySettings = \yii\helpers\ArrayHelper::getValue($setting, 'justfly_com');
            if (empty($justflySettings)) {
                return;
            }
            $justflySettings['name'] = 'Justify.com';
            unset($settingValue['justfly_com']);
            $settingValue['justify_com'] = $justflySettings;
            $setting->s_value            = json_encode($settingValue, true);
            $setting->save();
            if (Yii::$app->cache) {
                Yii::$app->cache->flush();
            }

            Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
        } catch (Throwable $throwable) {
            Yii::error(
                $throwable,
                'm220329_073203_fix_price_research_tool_justfly_name_settings:safeDown:Throwable'
            );
        }
    }
}
