<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m210920_062220_add_new_abac_premission_for_decline_reprotection_quotes
 */
class m210920_062220_add_new_abac_premission_for_decline_reprotection_quotes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.is_new == true) && ((r.sub.env.dt.year == 2021) || ("admin" in r.sub.env.user.roles))',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"product-quote/product-quote/is_new","field":"is_new","type":"boolean","input":"radio","operator":"==","value":true},{"condition":"OR","rules":[{"id":"env_dt_year","field":"env.dt.year","type":"integer","input":"number","operator":"==","value":2021},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}]}],"valid":true}',
            'ap_object' => 'product-quote/product-quote/act/reprotection_quote/decline',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to decline reprotection quotes in status new',
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
            'product-quote/product-quote/act/reprotection_quote/decline',
        ]]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
