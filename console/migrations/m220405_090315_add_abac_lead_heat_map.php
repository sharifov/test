<?php

use modules\abac\src\entities\AbacPolicy;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220405_090315_add_abac_lead_heat_map
 */
class m220405_090315_add_abac_lead_heat_map extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            if (!AbacPolicy::find()->where(['ap_object' => 'lead/lead/obj/heat_map_lead'])->exists()) {
                $this->insert(
                    '{{%abac_policy}}',
                    [
                        'ap_rule_type' => 'p',
                        'ap_subject' => '("admin" in r.sub.env.user.roles)',
                        'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
                        'ap_object' => 'lead/lead/obj/heat_map_lead',
                        'ap_action' => '(access)',
                        'ap_action_json' => "[\"access\"]",
                        'ap_effect' => 1,
                        'ap_title' => 'Heat Map Lead Report',
                        'ap_sort_order' => 50,
                        'ap_enabled' => 1,
                    ]
                );
                \Yii::$app->abac->invalidatePolicyCache();
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable, true), 'm220405_090315_add_abac_lead_heat_map:safeUp');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            if (AbacPolicy::deleteAll(['ap_object' => 'lead/lead/obj/heat_map_lead'])) {
                \Yii::$app->abac->invalidatePolicyCache();
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable, true), 'm220405_090315_add_abac_lead_heat_map:safeDown');
        }
    }
}
