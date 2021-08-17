<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m210817_064807_alter_abac_permissions_name
 */
class m210817_064807_alter_abac_permissions_name extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('{{%abac_policy}}', ['IN', 'ap_object', [
            'case/case/reprotection_quote/send_email',
            'case/case/product_quote/remove',
            'case/case/product_quote/view_details',
        ]]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(is_owner == true) || ("admin" in env.user.roles) || ("superadmin" in env.user.roles)',
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

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'case/case/act/product_quote/remove',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => 'Product Quote Remove in order block',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'case/case/act/product_quote/view_details',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => 'Product Quote View Details in order block',
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
            'case/case/act/reprotection_quote/send_email',
            'case/case/act/product_quote/remove',
            'case/case/act/product_quote/view_details',
        ]]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
