<?php

use common\models\Setting;
use yii\db\Migration;
use yii\helpers\Json;

/**
 * Class m220920_082635_add_new_parameters_to_cross_sale_setting
 */
class m220920_082635_add_new_parameters_to_cross_sale_setting extends Migration
{
    private const KEY = 'case_cross_sale_queue';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $setting = $this->getSetting();
        $value = Json::decode($setting['s_value'], true);
        $value = array_merge($value, [
            'excludeProducts' => [
                'travelInsurance',
            ],
            'projects' => [],
            'cabins' => [],
            'products' => [],
            'rushFlightsHours' => 0,
        ]);
        $description = "If the parameter without \"exclude\" is specified, then the exclusion list will be ignored.\n";
        $description .= "Allowed products: cfar, flexibleTicket, pdp, travelInsurance";

        $this->update(
            '{{%setting}}',
            [
                's_value' => json_encode($value),
                's_description' => $description,
            ],
            [
                's_key' => self::KEY,
            ]
        );

        $this->updateCache();
    }

    private function getSetting(): Setting
    {
        $setting = Setting::find()
            ->where([
                's_key' => self::KEY,
            ])
            ->limit(1)
            ->one();

        if ($setting === null) {
            throw new \Exception('Setting with key ' . self::KEY . ' not found');
        }

        return $setting;
    }

    private function updateCache(): void
    {
        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
        Yii::$app->cache->delete('site_settings');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $setting = $this->getSetting();
        $value = Json::decode($setting['s_value']);

        foreach (['excludeProducts', 'projects', 'cabins', 'products', 'rushFlightsHours'] as $parameter) {
            if (isset($value[$parameter])) {
                unset($value[$parameter]);
            }
        }

        $this->update(
            '{{%setting}}',
            [
                's_value' => Json::encode($value),
                's_description' => '',
            ],
            [
                's_key' => self::KEY,
            ]
        );

        $this->updateCache();
    }
}
