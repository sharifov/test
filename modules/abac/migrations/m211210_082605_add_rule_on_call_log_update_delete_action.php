<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m211210_082605_add_rule_on_call_log_update_delete_action
 */
class m211210_082605_add_rule_on_call_log_update_delete_action extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'call/call/obj/call-log',
            'ap_action' => '(update)|(delete)',
            'ap_action_json' => "[\"update\",\"delete\"]",
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
            'call/call/obj/call-log',
        ]], ['IN', 'ap_action', ['(update)|(delete)']]]);

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
