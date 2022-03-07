<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220304_074151_add_abac_premission_for_lead_trash_action_on_lead_view_page
 */
class m220304_074151_add_abac_premission_for_lead_trash_action_on_lead_view_page extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles) || ((r.sub.is_owner == true) && (r.sub.status_id == 2) && (r.sub.hasAppliedQuote == false))',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"condition":"AND","rules":[{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":2},{"id":"lead/lead/hasAppliedQuote","field":"hasAppliedQuote","type":"boolean","input":"radio","operator":"==","value":false}]}],"valid":true}',
            'ap_object' => 'lead/lead/obj/lead',
            'ap_action' => '(trash)',
            'ap_action_json' => "[\"trash\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to take move lead to trash status on lead view page',
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
        $this->delete('{{%abac_policy}}', [['IN', 'ap_object', [
            'lead/lead/obj/lead',
        ]], ['ap_action' => '(trash)']]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
