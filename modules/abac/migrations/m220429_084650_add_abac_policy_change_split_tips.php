<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220429_084650_add_abac_policy_change_split_tips
 */
class m220429_084650_add_abac_policy_change_split_tips extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.env.available == false)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":false}],"valid":true}',
            'ap_object' => 'lead/lead/change-split-tips',
            'ap_action' => '(update)',
            'ap_action_json' => "[\"update\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);
        \Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%abac_policy}}', ['AND', ['IN', 'ap_object', [
            'lead/lead/change-split-tips',
        ]], ['IN', 'ap_action', ['(update)']]]);

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
