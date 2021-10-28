<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m211022_120610_access_lead_actions_take_and_take_over_abac_rules
 */
class m211022_120610_access_lead_actions_take_and_take_over_abac_rules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.status_id != 16) && (r.sub.status_id != 1) && (r.sub.status_id != 5) && (r.sub.status_id != 8) && (r.sub.status_id != 11) && (r.sub.status_id != 13) && (r.sub.status_id != 14) && (r.sub.status_id != 15)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"!=","value":16},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"!=","value":1},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"!=","value":5},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"!=","value":8},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"!=","value":11},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"!=","value":13},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"!=","value":14},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"!=","value":15}],"valid":true}',
            'ap_object' => 'lead/lead/act/take-lead',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 0,
            'ap_title' => 'Take Lead action',
            'ap_sort_order' => 10,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("agent" in r.sub.env.user.roles) && (((r.sub.status_id == 1) && (r.sub.withinPersonalTakeLimits == false)) || ((r.sub.status_id == 14) && (r.sub.canTakeByFrequencyMinutes == false)))',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"agent"},{"condition":"OR","rules":[{"condition":"AND","rules":[{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":1},{"id":"lead/lead/withinPersonalTakeLimits","field":"withinPersonalTakeLimits","type":"boolean","input":"radio","operator":"==","value":false}]},{"condition":"AND","rules":[{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":14},{"id":"lead/lead/canTakeByFrequencyMinutes","field":"canTakeByFrequencyMinutes","type":"boolean","input":"radio","operator":"==","value":false}]}]}],"valid":true}',
            'ap_object' => 'lead/lead/act/take-lead',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 0,
            'ap_title' => 'Take Lead action',
            'ap_sort_order' => 20,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.is_owner == false) && (("admin" in r.sub.env.user.roles) || ((r.sub.isInProject == true) && (r.sub.isInDepartment == true)))',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":false},{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"condition":"AND","rules":[{"id":"lead/lead/isInProject","field":"isInProject","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/lead/isInDepartment","field":"isInDepartment","type":"boolean","input":"radio","operator":"==","value":true}]}]}],"valid":true}',
            'ap_object' => 'lead/lead/act/take-lead',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => 'Take Lead action',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%abac_policy}}', ['IN', 'ap_object', [
            'lead/lead/act/take-lead',
        ]]);
    }
}
