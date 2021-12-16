<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m211011_130246_update_abac_rule_send_schedule_change_email
 */
class m211011_130246_update_abac_rule_send_schedule_change_email extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('{{%abac_policy}}', ['IN', 'ap_object', [
            'case/case/reprotection_quote/send_email',
        ]]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '((r.sub.is_owner == true) && ((r.sub.pqc_status == 1) || (r.sub.pqc_status == 2))) || (("admin" in r.sub.env.user.roles) && ((r.sub.pqc_status == 1) || (r.sub.pqc_status == 2)))',
            'ap_subject_json' => '{"condition":"OR","rules":[{"condition":"AND","rules":[{"id":"case/case/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"condition":"OR","rules":[{"id":"case/case/pqc_status","field":"pqc_status","type":"integer","input":"select","operator":"==","value":1},{"id":"case/case/pqc_status","field":"pqc_status","type":"integer","input":"select","operator":"==","value":2}]}]},{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"condition":"OR","rules":[{"id":"case/case/pqc_status","field":"pqc_status","type":"integer","input":"select","operator":"==","value":1},{"id":"case/case/pqc_status","field":"pqc_status","type":"integer","input":"select","operator":"==","value":2}]}]}],"valid":true}',
            'ap_object' => 'case/case/act/reprotection_quote/send_email',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => 'Reprotection Quote Send Email',
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
            'case/case/reprotection_quote/send_email',
        ]]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.is_owner == true) || ("admin" in r.sub.env.user.roles) || ("superadmin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"case/case/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"superadmin"}],"valid":true}',
            'ap_object' => 'case/case/act/reprotection_quote/send_email',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => 'Reprotection Quote Send Email',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);
    }
}
