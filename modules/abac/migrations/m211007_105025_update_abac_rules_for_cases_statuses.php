<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m211007_105025_update_abac_rules_for_cases_statuses
 */
class m211007_105025_update_abac_rules_for_cases_statuses extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('{{%abac_policy}}', ['IN', 'ap_object', [
            'case/case/obj/in_pending',
            'case/case/obj/in_processing',
            'case/case/obj/in_follow_up',
            'case/case/obj/in_solved',
            'case/case/obj/in_trash',
            'case/case/obj/in_new',
            'case/case/obj/in_awaiting',
            'case/case/obj/in_auto_processing',
            'case/case/obj/in_error',
        ]]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.is_owner == true) || ("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"case/case/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'case/case/obj/status_rules',
            'ap_action' => '(transfer)',
            'ap_action_json' => "[\"transfer\"]",
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
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.is_owner == true) || ("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"case/case/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'case/case/obj/in_error',
            'ap_action' => '(transfer)',
            'ap_action_json' => "[\"transfer\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.is_owner == true) || ("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"case/case/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'case/case/obj/in_auto_processing',
            'ap_action' => '(transfer)',
            'ap_action_json' => "[\"transfer\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.is_owner == true) || ("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"case/case/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'case/case/obj/in_awaiting',
            'ap_action' => '(transfer)',
            'ap_action_json' => "[\"transfer\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.is_owner == true) || ("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"case/case/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'case/case/obj/in_new',
            'ap_action' => '(transfer)',
            'ap_action_json' => "[\"transfer\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.is_owner == true) || ("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"case/case/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'case/case/obj/in_trash',
            'ap_action' => '(transfer)',
            'ap_action_json' => "[\"transfer\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.is_owner == true) || ("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"case/case/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'case/case/obj/in_solved',
            'ap_action' => '(transfer)',
            'ap_action_json' => "[\"transfer\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.is_owner == true) || ("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"case/case/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'case/case/obj/in_follow_up',
            'ap_action' => '(transfer)',
            'ap_action_json' => "[\"transfer\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.is_owner == true) || ("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"case/case/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'case/case/obj/in_processing',
            'ap_action' => '(transfer)',
            'ap_action_json' => "[\"transfer\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.is_owner == true) || ("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"case/case/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'case/case/obj/in_pending',
            'ap_action' => '(transfer)',
            'ap_action_json' => "[\"transfer\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
