<?php

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220829_095135_add_abac_policy_for_user_stats
 */
class m220829_095135_add_abac_policy_for_user_stats extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $isInvalidate = false;
        if (!AbacPolicy::find()->where(['ap_object' => 'user-stats/user-stats/obj/user-stats', 'ap_subject' => '("admin" in env.user.roles)'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"not":false,"valid":true}',
                'ap_object' => 'user-stats/user-stats/obj/user-stats',
                'ap_action' => '(access)',
                'ap_action_json' => "[\"access\"]",
                'ap_effect' => 1,
                'ap_title' => 'Access to User Stats by Lead from report',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
            $isInvalidate = true;
        }
        if ($isInvalidate) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (AbacPolicy::deleteAll(['ap_object' => 'user-stats/user-stats/obj/user-stats', 'ap_action' => '(access)'])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
