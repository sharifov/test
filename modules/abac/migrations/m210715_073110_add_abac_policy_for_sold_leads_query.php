<?php

namespace modules\abac\migrations;

use yii\db\Migration;
use Yii;

/**
 * Class m210715_073110_add_abac_policy_for_sold_leads_query
 */
class m210715_073110_add_abac_policy_for_sold_leads_query extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'lead/lead/query/sold/*',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("data_analyst" in r.sub.env.user.roles) || ("exchange_senior" in r.sub.env.user.roles) || ("qa" in r.sub.env.user.roles) || ("qa_super" in r.sub.env.user.roles) || ("sales_senior" in r.sub.env.user.roles) || ("supervision" in r.sub.env.user.roles) || ("support_senior" in r.sub.env.user.roles) || ("agent" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"data_analyst"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"exchange_senior"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"qa"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"qa_super"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"sales_senior"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"supervision"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"support_senior"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"agent"}],"valid":true}',
            'ap_object' => 'lead/lead/query/sold/on_common_projects',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'lead/lead/query/sold/on_common_departments',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 0,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'lead/lead/query/sold/on_common_groups',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 0,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles) || ("data_analyst" in r.sub.env.user.roles) || ("exchange_senior" in r.sub.env.user.roles) || ("qa" in r.sub.env.user.roles) || ("qa_super" in r.sub.env.user.roles) || ("sales_senior" in r.sub.env.user.roles) || ("supervision" in r.sub.env.user.roles) || ("support_senior" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"data_analyst"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"exchange_senior"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"qa"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"qa_super"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"sales_senior"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"supervision"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"support_senior"}],"valid":true}',
            'ap_object' => 'lead/lead/query/sold/is_owner',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles) && (r.sub.has_owner_query == true)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"lead/lead/has_owner_query","field":"has_owner_query","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'lead/lead/query/sold/is_empty_owner',
            'ap_action' => '(and)',
            'ap_action_json' => "[\"and\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 0,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles) && (r.sub.has_owner_query == false)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"lead/lead/has_owner_query","field":"has_owner_query","type":"boolean","input":"radio","operator":"==","value":false}],"valid":true}',
            'ap_object' => 'lead/lead/query/sold/is_empty_owner',
            'ap_action' => '(or)',
            'ap_action_json' => "[\"and\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 0,
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
            'lead/lead/query/sold/*',
            'lead/lead/query/sold/on_common_projects',
            'lead/lead/query/sold/on_common_departments',
            'lead/lead/query/sold/on_common_groups',
            'lead/lead/query/sold/is_owner',
            'lead/lead/query/sold/is_empty_owner'
        ]]);
        Yii::$app->abac->invalidatePolicyCache();
    }
}
