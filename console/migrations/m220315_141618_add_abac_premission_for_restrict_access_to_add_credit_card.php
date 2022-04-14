<?php

use yii\db\Migration;

/**
 * Class m220315_141618_add_abac_premission_for_restrict_access_to_add_credit_card
 */
class m220315_141618_add_abac_premission_for_restrict_access_to_add_credit_card extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles) || (r.sub.is_owner == true)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"case/case/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'case/case/ui/block/sale-list',
            'ap_action' => '(Add Credit Card)|(Send CC Info)',
            'ap_action_json' => "[\"Add Credit Card\",\"Send CC Info\"]",
            'ap_effect' => 1,
            'ap_title' => 'Permission to restrict access to add credit card',
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
        $this->delete('{{%abac_policy}}', [['IN', 'ap_object', ['case/case/ui/block/sale-list',]],['ap_action' => '(Add Credit Card)|(Send CC Info)']]);

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
