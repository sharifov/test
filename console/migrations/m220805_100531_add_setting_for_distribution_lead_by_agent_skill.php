<?php

use common\models\Setting;
use common\models\UserProfile;
use yii\db\Migration;
use yii\helpers\Json;

/**
 * Class m220805_100531_add_setting_for_distribution_lead_by_agent_skill
 */
class m220805_100531_add_setting_for_distribution_lead_by_agent_skill extends Migration
{
    private const SETTING_KEY = 'smart_lead_distribution_by_agent_skill_and_points';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $description = UserProfile::SKILL_TYPE_JUNIOR . " = Junior\n";
        $description .= UserProfile::SKILL_TYPE_MIDDLE . " = Middle\n";
        $description .= UserProfile::SKILL_TYPE_SENIOR . ' = Senior';

        $this->insert(
            '{{%setting}}',
            [
                's_key' => self::SETTING_KEY,
                's_name' => 'Lead distribution by agent skill and total lead points',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => Json::encode([
                    'business' => [
                        UserProfile::SKILL_TYPE_SENIOR => [
                            'from' => 0,
                            'to' => 57
                        ],
                        UserProfile::SKILL_TYPE_MIDDLE => [
                            'from' => 0,
                            'to' => 43
                        ],
                        UserProfile::SKILL_TYPE_JUNIOR => [
                            'from' => 0,
                            'to' => 35
                        ],
                    ],
                ]),
                's_description' => $description,
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
