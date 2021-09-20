<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m210920_093639_add_new_abac_permission_for_link_lead_to_call
 */
class m210920_093639_add_new_abac_permission_for_link_lead_to_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles) && (r.sub.env.dt.year == 2021)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"env_dt_year","field":"env.dt.year","type":"integer","input":"number","operator":"==","value":2021}],"valid":true}',
            'ap_object' => 'lead/lead/act/link-to-call',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to link lead to call',
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
        $this->delete('{{%abac_policy}}', ['IN', 'ap_object', [
            'product-lead/lead/act/link-to-call',
        ]]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
