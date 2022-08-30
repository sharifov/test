<?php

use common\models\Setting;
use yii\db\Migration;
use yii\helpers\Json;

/**
 * Class m220816_054907_add_virtual_agent_list_to_site_settings
 */
class m220816_054907_add_virtual_agent_list_to_site_settings extends Migration
{
    private const SETTING_KEY = 'virtual_agent_list';

    public function safeUp()
    {
        $this->insert(
            '{{%setting}}',
            [
                's_key' => self::SETTING_KEY,
                's_name' => 'Virtual agent list',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => Json::encode([
                    'ovago' => null,
                    'wowfare' => null,
                    'arangrant' => null,
                    'hop2' => null,
                    'gurufare' => null,
                    'flygtravel' => null,
                    'wowgateway' => null,
                    'priceline' => null,
                    'acapulcovuelos' => null,
                    'kayak' => null,
                    'airandtour' => null,
                    'scholartrip' => null,
                    'allsavingstravel' => null,
                    'papayatrip' => null,
                    'chatdeal' => null,
                    'airclass' => null,
                ]),
                's_description' => 'ProjectKey: employeeUsername',
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => null,
            ]
        );

        $this->clearCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            self::SETTING_KEY,
        ]]);

        $this->clearCache();
    }

    private function clearCache()
    {
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
