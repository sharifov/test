<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m211228_131033_update_abac_rules_client_info_block
 */
class m211228_131033_update_abac_rules_client_info_block extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('{{%abac_policy}}', ['IN', 'ap_object', [
            'lead/lead/act/client-details',
            'lead/lead/act/client-add-phone',
            'lead/lead/act/client-edit-phone',
            'lead/lead/act/user-same-phone-info',
            'lead/lead/act/client-add-email',
            'lead/lead/act/client-edit-email',
            'lead/lead/act/user-same-email-info',
            'lead/lead/act/client-update',
            'lead/lead/act/client-subscribe',
            'lead/lead/act/client-unsubscribe',
            'lead/lead/act/search-leads-by-ip',
            'lead/lead/ui/field/phone',
            'lead/lead/ui/field/email',
            'lead/lead/ui/field/locale',
            'lead/lead/ui/field/marketing_country',
            'lead/lead/ui/menu/client-info'
        ]]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles || "qa" in r.sub.env.user.roles || "qa_super" in r.sub.env.user.roles || "sales_senior" in r.sub.env.user.roles) || (r.sub.is_owner == true)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["admin","qa","qa_super","sales_senior"]},{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'lead/lead/ui/block/client-info',
            'ap_action' => '(accessDetails)',
            'ap_action_json' => "[\"accessDetails\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles || "sales_senior" in r.sub.env.user.roles) || (r.sub.is_owner == true)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["admin","sales_senior"]},{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'lead/lead/ui/block/client-info',
            'ap_action' => '(accessAddPhone)|(addPhone)',
            'ap_action_json' => "[\"accessAddPhone\",\"addPhone\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles || "ex_super" in r.sub.env.user.roles || "qa" in r.sub.env.user.roles || "qa_super" in r.sub.env.user.roles || "sales_senior" in r.sub.env.user.roles || "sup_super" in r.sub.env.user.roles) || (("supervision" in r.sub.env.user.roles) && (r.sub.has_owner == true) && (r.sub.is_common_group == true)) || (r.sub.is_owner == true)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["admin","ex_super","qa","qa_super","sales_senior","sup_super"]},{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"supervision"},{"id":"lead/lead/has_owner","field":"has_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/lead/is_common_group","field":"is_common_group","type":"boolean","input":"radio","operator":"==","value":true}]},{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'lead/lead/ui/block/client-info',
            'ap_action' => '(accessEditPhone)|(editPhone)',
            'ap_action_json' => "[\"accessEditPhone\",\"editPhone\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles || "qa" in r.sub.env.user.roles || "qa_super" in r.sub.env.user.roles) || (r.sub.is_owner == true)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["admin","qa","qa_super"]},{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'lead/lead/ui/block/client-info',
            'ap_action' => '(accessUserSamePhoneInfo)',
            'ap_action_json' => "[\"accessUserSamePhoneInfo\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles || "sales_senior" in r.sub.env.user.roles) || (r.sub.is_owner == true)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["admin","sales_senior"]},{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'lead/lead/ui/block/client-info',
            'ap_action' => '(accessAddEmail)|(addEmail)',
            'ap_action_json' => "[\"accessAddEmail\",\"addEmail\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles || "ex_super" in r.sub.env.user.roles || "qa" in r.sub.env.user.roles || "qa_super" in r.sub.env.user.roles || "sales_senior" in r.sub.env.user.roles || "sup_super" in r.sub.env.user.roles) || (("supervision" in r.sub.env.user.roles) && (r.sub.has_owner == true) && (r.sub.is_common_group == true)) || (r.sub.is_owner == true)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["admin","ex_super","qa","qa_super","sales_senior","sup_super"]},{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"supervision"},{"id":"lead/lead/has_owner","field":"has_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/lead/is_common_group","field":"is_common_group","type":"boolean","input":"radio","operator":"==","value":true}]},{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'lead/lead/ui/block/client-info',
            'ap_action' => '(accessEditEmail)|(editEmail)',
            'ap_action_json' => "[\"accessEditEmail\",\"editEmail\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles || "qa" in r.sub.env.user.roles || "qa_super" in r.sub.env.user.roles) || (r.sub.is_owner == true)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["admin","qa","qa_super"]},{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'lead/lead/ui/block/client-info',
            'ap_action' => '(accessUserSameEmailInfo)',
            'ap_action_json' => "[\"accessUserSameEmailInfo\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles || "sales_senior" in r.sub.env.user.roles) || (r.sub.is_owner == true)',
            'ap_subject_json' => '"{\"condition\":\"OR\",\"rules\":[{\"id\":\"env_user_multi_roles\",\"field\":\"env.user.roles\",\"type\":\"string\",\"input\":\"select\",\"operator\":\"contains\",\"value\":[\"admin\",\"sales_senior\"]},{\"id\":\"lead/lead/is_owner\",\"field\":\"is_owner\",\"type\":\"boolean\",\"input\":\"radio\",\"operator\":\"==\",\"value\":true}],\"valid\":true}"',
            'ap_object' => 'lead/lead/ui/block/client-info',
            'ap_action' => '(accessUpdateClient)',
            'ap_action_json' => "[\"accessUpdateClient\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles || "sales_senior" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["admin","sales_senior"]}],"valid":true}',
            'ap_object' => 'lead/lead/ui/block/client-info',
            'ap_action' => '(subscribe)',
            'ap_action_json' => "[\"subscribe\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles || "qa_super" in r.sub.env.user.roles || "sales_senior" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["admin","qa_super","sales_senior"]}],"valid":true}',
            'ap_object' => 'lead/lead/ui/block/client-info',
            'ap_action' => '(unsubscribe)',
            'ap_action_json' => "[\"unsubscribe\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles) || (r.sub.is_owner == true)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'lead/lead/ui/block/client-info',
            'ap_action' => '(showLeadsByIp)',
            'ap_action_json' => "[\"showLeadsByIp\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '((r.sub.formAttribute == "phone") && (r.sub.isNewRecord == true) && (("admin" in r.sub.env.user.roles || "ex_super" in r.sub.env.user.roles || "qa_super" in r.sub.env.user.roles || "sales_senior" in r.sub.env.user.roles || "sup_super" in r.sub.env.user.roles || "supervision" in r.sub.env.user.roles) || (r.sub.is_owner == true))) || ((r.sub.formAttribute == "phone") && (r.sub.isNewRecord == false) && (("admin" in r.sub.env.user.roles || "client_chat_senior" in r.sub.env.user.roles || "qa" in r.sub.env.user.roles || "qa_super" in r.sub.env.user.roles || "sales_senior" in r.sub.env.user.roles || "supervision" in r.sub.env.user.roles) || (("client_chat_agent_expert" in r.sub.env.user.roles) && (r.sub.is_owner == true))))',
            'ap_subject_json' => '{"condition":"OR","rules":[{"condition":"AND","rules":[{"id":"lead/lead/formAttribute","field":"formAttribute","type":"string","input":"select","operator":"==","value":"phone"},{"id":"lead/lead/isNewRecord","field":"isNewRecord","type":"boolean","input":"radio","operator":"==","value":true},{"condition":"OR","rules":[{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["admin","ex_super","qa_super","sales_senior","sup_super","supervision"]},{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true}]}]},{"condition":"AND","rules":[{"id":"lead/lead/formAttribute","field":"formAttribute","type":"string","input":"select","operator":"==","value":"phone"},{"id":"lead/lead/isNewRecord","field":"isNewRecord","type":"boolean","input":"radio","operator":"==","value":false},{"condition":"OR","rules":[{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["admin","client_chat_senior","qa","qa_super","sales_senior","supervision"]},{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"client_chat_agent_expert"},{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true}]}]}]}],"valid":true}',
            'ap_object' => 'lead/lead/form/phone_create',
            'ap_action' => '(edit)',
            'ap_action_json' => "[\"edit\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '((r.sub.formAttribute == "email") && (r.sub.isNewRecord == true) && (("admin" in r.sub.env.user.roles || "ex_super" in r.sub.env.user.roles || "qa_super" in r.sub.env.user.roles || "sup_super" in r.sub.env.user.roles || "supervision" in r.sub.env.user.roles) || (r.sub.is_owner == true))) || ((r.sub.formAttribute == "email") && (r.sub.isNewRecord == false) && (("admin" in r.sub.env.user.roles || "client_chat_senior" in r.sub.env.user.roles || "qa" in r.sub.env.user.roles || "qa_super" in r.sub.env.user.roles || "sales_senior" in r.sub.env.user.roles) || (("client_chat_agent_expert" in r.sub.env.user.roles) || (r.sub.is_owner == true))))',
            'ap_subject_json' => '{"condition":"OR","rules":[{"condition":"AND","rules":[{"id":"lead/lead/formAttribute","field":"formAttribute","type":"string","input":"select","operator":"==","value":"email"},{"id":"lead/lead/isNewRecord","field":"isNewRecord","type":"boolean","input":"radio","operator":"==","value":true},{"condition":"OR","rules":[{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["admin","ex_super","qa_super","sup_super","supervision"]},{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true}]}]},{"condition":"AND","rules":[{"id":"lead/lead/formAttribute","field":"formAttribute","type":"string","input":"select","operator":"==","value":"email"},{"id":"lead/lead/isNewRecord","field":"isNewRecord","type":"boolean","input":"radio","operator":"==","value":false},{"condition":"OR","rules":[{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["admin","client_chat_senior","qa","qa_super","sales_senior"]},{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"client_chat_agent_expert"},{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true}]}]}]}],"valid":true}',
            'ap_object' => 'lead/lead/form/email_create',
            'ap_action' => '(edit)',
            'ap_action_json' => "[\"edit\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles) && ((r.sub.formAttribute == "locale") || (r.sub.formAttribute == "marketingCountry"))',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"condition":"OR","rules":[{"id":"lead/lead/formAttribute","field":"formAttribute","type":"string","input":"select","operator":"==","value":"locale"},{"id":"lead/lead/formAttribute","field":"formAttribute","type":"string","input":"select","operator":"==","value":"marketingCountry"}]}],"valid":true}',
            'ap_object' => 'lead/lead/form/client_create',
            'ap_action' => '(edit)',
            'ap_action_json' => "[\"edit\"]",
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
    }
}
