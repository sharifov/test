<?php

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m211214_121933_add_default_abac_lead_data
 */
class m211214_121933_add_default_abac_lead_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!AbacPolicy::find()->where(['ap_object' => 'lead-data/lead-data/ui/info'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
                'ap_object' => 'lead-data/lead-data/ui/info',
                'ap_action' => '(read)',
                'ap_action_json' => "[\"read\"]",
                'ap_effect' => 1,
                'ap_title' => 'Show Lead Data info',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (AbacPolicy::deleteAll(['ap_object' => 'lead-data/lead-data/ui/info'])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
