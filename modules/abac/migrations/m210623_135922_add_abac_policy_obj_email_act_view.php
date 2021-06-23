<?php

namespace modules\abac\migrations;

use yii\db\Migration;
use Yii;

/**
 * Class m210623_135922_add_abac_policy_obj_email_act_view
 */
class m210623_135922_add_abac_policy_obj_email_act_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles) || (("agent" in r.sub.env.user.roles) && ((r.sub.is_address_owner == true) || (r.sub.is_email_owner == true) || (r.sub.is_lead_owner == true))) || (("ex_agent" in r.sub.env.user.roles) && (((r.sub.is_lead_owner == true) || (r.sub.is_case_owner == true)) || (r.sub.is_address_owner == true) || (r.sub.is_email_owner == true))) || ("ex_super" in r.sub.env.user.roles) || ("exchange_senior" in r.sub.env.user.roles) || ("qa" in r.sub.env.user.roles) || ("qa_super" in r.sub.env.user.roles) || ("sales_senior" in r.sub.env.user.roles) || (("sup_agent" in r.sub.env.user.roles) && (((r.sub.is_lead_owner == true) || (r.sub.is_case_owner == true)) || (r.sub.is_address_owner == true) || (r.sub.is_email_owner == true))) || ("sup_super" in r.sub.env.user.roles) || ("supervision" in r.sub.env.user.roles) || ("support_qa" in r.sub.env.user.roles) || ("support_senior" in r.sub.env.user.roles) || ("userManager" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"agent"},{"condition":"OR","rules":[{"id":"email/is_address_owner","field":"is_address_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"email/is_email_owner","field":"is_email_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"email/is_lead_owner","field":"is_lead_owner","type":"boolean","input":"radio","operator":"==","value":true}]}]},{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"ex_agent"},{"condition":"OR","rules":[{"condition":"OR","rules":[{"id":"email/is_lead_owner","field":"is_lead_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"email/is_case_owner","field":"is_case_owner","type":"boolean","input":"radio","operator":"==","value":true}]},{"id":"email/is_address_owner","field":"is_address_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"email/is_email_owner","field":"is_email_owner","type":"boolean","input":"radio","operator":"==","value":true}]}]},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"ex_super"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"exchange_senior"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"qa"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"qa_super"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"sales_senior"},{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"sup_agent"},{"condition":"OR","rules":[{"condition":"OR","rules":[{"id":"email/is_lead_owner","field":"is_lead_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"email/is_case_owner","field":"is_case_owner","type":"boolean","input":"radio","operator":"==","value":true}]},{"id":"email/is_address_owner","field":"is_address_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"email/is_email_owner","field":"is_email_owner","type":"boolean","input":"radio","operator":"==","value":true}]}]},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"sup_super"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"supervision"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"support_qa"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"support_senior"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"userManager"}],"valid":true}',
            'ap_object' => 'email/act/view',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => '',
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
            'email/act/view'
        ]]);
        Yii::$app->abac->invalidatePolicyCache();
    }
}
