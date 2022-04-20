<?php

use common\models\Setting;
use frontend\helpers\JsonHelper;
use modules\webEngage\settings\WebEngageDictionary;
use yii\db\Migration;
use yii\helpers\Console;

/**
 * Class m211208_124559_add_setting_web_engage_create_lead
 */
class m211208_124559_add_setting_web_engage_create_lead extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($webEngage = Setting::findOne(['s_key' => 'web_engage'])) {
            $value = JsonHelper::decode($webEngage->s_value);
            $value[WebEngageDictionary::EVENT_LEAD_CREATED]['apiUsernames'] = '';
            $webEngage->s_value = JsonHelper::encode($value);

            if (!$webEngage->save()) {
                \Yii::error([
                    'message' => 'Setting web_engage not saved',
                    'model' => $webEngage->getAttributes(),
                    'errors' => $webEngage->getErrors(),
                ], 'm211208_124559_add_setting_web_engage_create_lead');
                echo Console::renderColoredString('%r --- Error : %pSetting web_engage not saved%n'), PHP_EOL;
            }
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211208_124559_add_setting_web_engage_create_lead cannot be reverted.\n";
    }
}
