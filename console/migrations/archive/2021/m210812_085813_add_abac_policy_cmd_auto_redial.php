<?php

use yii\db\Migration;

/**
 * Class m210812_085813_add_abac_policy_cmd_auto_redial
 */
class m210812_085813_add_abac_policy_cmd_auto_redial extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => "{\"condition\":\"AND\",\"rules\":[{\"id\":\"env_user_roles\",\"field\":\"env.user.roles\",\"type\":\"string\",\"input\":\"select\",\"operator\":\"in_array\",\"value\":\"admin\"}],\"valid\":true}",
            'ap_object' => 'lead/lead/cmd/auto_redial',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to Lead Auto redial',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);
        Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%abac_policy}}', ['IN', 'ap_object', [
            'lead/lead/cmd/auto_redial'
        ]]);
        Yii::$app->abac->invalidatePolicyCache();
    }
}
