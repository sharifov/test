<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m211206_102546_add_rule_on_lead_clone_action
 */
class m211206_102546_add_rule_on_lead_clone_action extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles) || ((r.sub.isInProject == true) && (r.sub.isInDepartment == true))',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"condition":"AND","rules":[{"id":"lead/lead/isInProject","field":"isInProject","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/lead/isInDepartment","field":"isInDepartment","type":"boolean","input":"radio","operator":"==","value":true}]}],"valid":true}',
            'ap_object' => 'lead/lead/obj/lead',
            'ap_action' => '(clone)',
            'ap_action_json' => "[\"clone\"]",
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
            'lead/lead/obj/lead',
        ]], ['IN', 'ap_action', ['(clone)']]]);

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
