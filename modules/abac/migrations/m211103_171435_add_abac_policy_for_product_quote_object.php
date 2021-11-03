<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m211103_171435_add_abac_policy_for_product_quote_object
 */
class m211103_171435_add_abac_policy_for_product_quote_object extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.env.available == true)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'product-quote/product-quote/act/view_details',
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
            'ap_subject' => '(("admin" in r.sub.env.user.roles) || (r.sub.isCaseOwner == true)) && (r.sub.isPqChangeable == true) && (r.sub.hasPqcActive == false) && (r.sub.hasPqrActive == false)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"product-quote/product-quote/isCaseOwner","field":"isCaseOwner","type":"boolean","input":"radio","operator":"==","value":true}]},{"id":"product-quote/product-quote/isPqChangeable","field":"isPqChangeable","type":"boolean","input":"radio","operator":"==","value":true},{"id":"product-quote/product-quote/hasPqcActive","field":"hasPqcActive","type":"boolean","input":"radio","operator":"==","value":false},{"id":"product-quote/product-quote/hasPqrActive","field":"hasPqrActive","type":"boolean","input":"radio","operator":"==","value":false}],"valid":true}',
            'ap_object' => 'product-quote/product-quote/act/add_change',
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
            'ap_subject' => '(("admin" in r.sub.env.user.roles) || (r.sub.isCaseOwner == true)) && (r.sub.isPqChangeable == true) && (r.sub.hasPqrActive == false) && (r.sub.hasPqcActive == false)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"product-quote/product-quote/isCaseOwner","field":"isCaseOwner","type":"boolean","input":"radio","operator":"==","value":true}]},{"id":"product-quote/product-quote/isPqChangeable","field":"isPqChangeable","type":"boolean","input":"radio","operator":"==","value":true},{"id":"product-quote/product-quote/hasPqrActive","field":"hasPqrActive","type":"boolean","input":"radio","operator":"==","value":false},{"id":"product-quote/product-quote/hasPqcActive","field":"hasPqcActive","type":"boolean","input":"radio","operator":"==","value":false}],"valid":true}',
            'ap_object' => 'product-quote/product-quote/act/create_voluntary_quote_refund',
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
            'ap_object' => 'product-quote/product-quote/act/remove',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
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
        $this->delete('{{%abac_policy}}', ['IN', 'ap_object', [
            'product-quote/product-quote/act/view_details',
            'product-quote/product-quote/act/add_change',
            'product-quote/product-quote/act/create_voluntary_quote_refund',
            'product-quote/product-quote/act/remove',
        ]]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
