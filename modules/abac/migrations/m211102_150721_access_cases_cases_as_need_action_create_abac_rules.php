<?php

namespace modules\abac\migrations;

use yii\db\Migration;
use Yii;

/**
 * Class m211102_150721_access_cases_cases_as_need_action_create_abac_rules
 */
class m211102_150721_access_cases_cases_as_need_action_create_abac_rules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles || "ex_super" in r.sub.env.user.roles || "exchange_senior" in r.sub.env.user.roles || "sup_super" in r.sub.env.user.roles || "superadmin" in r.sub.env.user.roles || "support_senior" in r.sub.env.user.roles || "test1_role" in r.sub.env.user.roles) && ((r.sub.env.req.action == "cases-q/need-action") || (r.sub.env.req.action == "cases-q-counters/get-q-count"))',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["admin","ex_super","exchange_senior","sup_super","superadmin","support_senior","test1_role"]},{"condition":"OR","rules":[{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q/need-action"},{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q-counters/get-q-count"}]}],"valid":true}',
            'ap_object' => 'case/case/sql/queue',
            'ap_action' => '(emptyOwnerAccess)',
            'ap_action_json' => "[\"emptyOwnerAccess\"]",
            'ap_effect' => 1,
            'ap_title' => 'Cases need action emptyOwnerAccess',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles || "ex_agent" in r.sub.env.user.roles || "exchange_senior" in r.sub.env.user.roles || "schd_agent" in r.sub.env.user.roles || "sup_agent" in r.sub.env.user.roles || "sup_super" in r.sub.env.user.roles || "support_senior" in r.sub.env.user.roles) && ((r.sub.env.req.action == "cases-q/need-action") || (r.sub.env.req.action == "cases-q-counters/get-q-count"))',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["admin","ex_agent","exchange_senior","schd_agent","sup_agent","sup_super","support_senior"]},{"condition":"OR","rules":[{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q/need-action"},{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q-counters/get-q-count"}]}],"valid":true}',
            'ap_object' => 'case/case/sql/queue',
            'ap_action' => '(ownerAccess)',
            'ap_action_json' => "[\"ownerAccess\"]",
            'ap_effect' => 1,
            'ap_title' => 'Cases need action ownerAccess',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles || "ex_super" in r.sub.env.user.roles || "exchange_senior" in r.sub.env.user.roles || "sup_super" in r.sub.env.user.roles || "support_senior" in r.sub.env.user.roles) && ((r.sub.env.req.action == "cases-q/need-action") || (r.sub.env.req.action == "cases-q-counters/get-q-count"))',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["admin","ex_super","exchange_senior","sup_super","support_senior"]},{"condition":"OR","rules":[{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q/need-action"},{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q-counters/get-q-count"}]}],"valid":true}',
            'ap_object' => 'case/case/sql/queue',
            'ap_action' => '(groupAccess)',
            'ap_action_json' => "[\"groupAccess\"]",
            'ap_effect' => 1,
            'ap_title' => 'Cases need action groupAccess',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles || "exchange_senior" in r.sub.env.user.roles || "support_qa" in r.sub.env.user.roles || "support_senior" in r.sub.env.user.roles) && ((r.sub.env.req.action == "cases-q/need-action") || (r.sub.env.req.action == "cases-q-counters/get-q-count"))',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["admin","exchange_senior","support_qa","support_senior"]},{"condition":"OR","rules":[{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q/need-action"},{"id":"env_action","field":"env.req.action","type":"string","input":"text","operator":"==","value":"cases-q-counters/get-q-count"}]}],"valid":true}',
            'ap_object' => 'case/case/sql/queue',
            'ap_action' => '(allAccess)',
            'ap_action_json' => "[\"allAccess\"]",
            'ap_effect' => 1,
            'ap_title' => 'Cases need action allAccess',
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
            'case/case/sql/queue',
        ]]);
        Yii::$app->abac->invalidatePolicyCache();
    }
}
