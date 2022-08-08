<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m220805_085849_add_setting_for_smart_lead_distribution
 */
class m220805_085849_add_setting_for_smart_lead_distribution extends Migration
{
    private const SETTING_KEY = 'smart_lead_distribution_rating_categories';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            '{{%setting}}',
            [
                's_key' => self::SETTING_KEY,
                's_name' => 'Categories for Smart Lead Distribution with total scores',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => \yii\helpers\Json::encode([
                    1 => [
                        'name' => 'CAT I',
                        'points' => [
                            'from' => 44,
                            'to' => 57
                        ],
                    ],
                    2 => [
                        'name' => 'CAT II',
                        'points' => [
                            'from' => 36,
                            'to' => 43
                        ],
                    ],
                    3 => [
                        'name' => 'CAT III',
                        'points' => [
                            'from' => 0,
                            'to' => 35
                        ],
                    ],
                ]),
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
