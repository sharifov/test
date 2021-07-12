<?php

namespace modules\abac\migrations;

use yii\db\Migration;
use Yii;

/**
 * Class m210706_101218_add_abac_policy_for_ui_block_client_info
 */
class m210706_101218_add_abac_policy_for_ui_block_client_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles) || (("agent" in r.sub.env.user.roles) && (r.sub.is_owner == true)) || ("data_analyst" in r.sub.env.user.roles) || (("ex_agent" in r.sub.env.user.roles) && (r.sub.is_owner == true)) || (("ex_super" in r.sub.env.user.roles) && ((r.sub.is_owner == true) || (r.sub.has_owner == false) || (r.sub.is_common_group == true))) || ("exchange_senior" in r.sub.env.user.roles) || ("facebook_marketing" in r.sub.env.user.roles) || ("qa" in r.sub.env.user.roles) || ("qa_super" in r.sub.env.user.roles) || ("sales_senior" in r.sub.env.user.roles) || (("schd_agent" in r.sub.env.user.roles) && (r.sub.is_owner == true)) || (("schd_super" in r.sub.env.user.roles) && ((r.sub.is_owner == true) || (r.sub.has_owner == false) || (r.sub.is_common_group == true))) || (("sup_agent" in r.sub.env.user.roles) && (r.sub.is_owner == true)) || (("sup_super" in r.sub.env.user.roles) && ((r.sub.is_owner == true) || (r.sub.has_owner == false) || (r.sub.is_common_group == true))) || (("supervision" in r.sub.env.user.roles) && ((r.sub.is_owner == true) || (r.sub.has_owner == false) || (r.sub.is_common_group == true))) || ("support_qa" in r.sub.env.user.roles) || ("support_senior" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"agent"},{"id":"lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true}]},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"data_analyst"},{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"ex_agent"},{"id":"lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true}]},{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"ex_super"},{"condition":"OR","rules":[{"id":"lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/has_owner","field":"has_owner","type":"boolean","input":"radio","operator":"==","value":false},{"id":"lead/is_common_group","field":"is_common_group","type":"boolean","input":"radio","operator":"==","value":true}]}]},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"exchange_senior"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"facebook_marketing"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"qa"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"qa_super"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"sales_senior"},{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"schd_agent"},{"id":"lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true}]},{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"schd_super"},{"condition":"OR","rules":[{"id":"lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/has_owner","field":"has_owner","type":"boolean","input":"radio","operator":"==","value":false},{"id":"lead/is_common_group","field":"is_common_group","type":"boolean","input":"radio","operator":"==","value":true}]}]},{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"sup_agent"},{"id":"lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true}]},{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"sup_super"},{"condition":"OR","rules":[{"id":"lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/has_owner","field":"has_owner","type":"boolean","input":"radio","operator":"==","value":false},{"id":"lead/is_common_group","field":"is_common_group","type":"boolean","input":"radio","operator":"==","value":true}]}]},{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"supervision"},{"condition":"OR","rules":[{"id":"lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/has_owner","field":"has_owner","type":"boolean","input":"radio","operator":"==","value":false},{"id":"lead/is_common_group","field":"is_common_group","type":"boolean","input":"radio","operator":"==","value":true}]}]},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"support_qa"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"support_senior"}],"valid":true}',
            'ap_object' => 'lead/ui/block/client-info',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to client info block',
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
            'lead/ui/block/client-info'
        ]]);
        Yii::$app->abac->invalidatePolicyCache();
    }
}
