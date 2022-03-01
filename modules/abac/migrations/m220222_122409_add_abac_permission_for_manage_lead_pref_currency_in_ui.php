<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220222_122409_add_abac_permission_for_manage_lead_pref_currency_in_ui
 */
class m220222_122409_add_abac_permission_for_manage_lead_pref_currency_in_ui extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.status_id == 2) && ("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":2},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'lead/lead/obj/lead_preferences',
            'ap_action' => '(manageLeadPrefCurrency)',
            'ap_action_json' => "[\"manageLeadPrefCurrency\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to manage lead preferences currency',
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
            'lead/lead/obj/lead_preferences',
        ]]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
