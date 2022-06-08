<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m220603_125208_alter_setting_redial_lead_exclude_attributes
 */
class m220603_125208_alter_setting_redial_lead_exclude_attributes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $setting = (new Query())
            ->andWhere(['s_key' => 'redial_lead_exclude_attributes'])
            ->from('{{%setting}}')->one();

        try {
            if ($setting) {
                $params = json_decode($setting['s_value'], true, 512, JSON_THROW_ON_ERROR);
                $params['sources'] = [];

                (new Query())->createCommand()->update('{{%setting}}', [
                    's_value' => json_encode($params)
                ], ['s_id' => (int)$setting['s_id']])->execute();

                if ($cache = Yii::$app->cache) {
                    $cache->delete('site_settings');
                }
            }
        } catch (Throwable $e) {
            echo $e->getMessage();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $setting = (new Query())
            ->andWhere(['s_key' => 'redial_lead_exclude_attributes'])
            ->from('{{%setting}}')->one();

        try {
            if ($setting) {
                $params = json_decode($setting['s_value'], true, 512, JSON_THROW_ON_ERROR);

                if (isset($params['sources'])) {
                    unset($params['sources']);
                }

                (new Query())->createCommand()->update('{{%setting}}', [
                    's_value' => json_encode($params)
                ], ['s_id' => (int)$setting['s_id']])->execute();

                if ($cache = Yii::$app->cache) {
                    $cache->delete('site_settings');
                }
            }
        } catch (Throwable $e) {
            echo $e->getMessage();
        }
    }
}
