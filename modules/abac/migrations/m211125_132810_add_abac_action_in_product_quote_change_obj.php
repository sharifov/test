<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m211125_132810_add_abac_action_in_product_quote_change_obj
 */
class m211125_132810_add_abac_action_in_product_quote_change_obj extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('{{%abac_policy}}', ['AND', ['IN', 'ap_object', [
            'product/product-quote-change/obj/product-quote-change',
        ]], ['IN', 'ap_action', ['(createReProtectionQuote)|(createVoluntaryQuote)']]]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(("admin" in r.sub.env.user.roles) || (r.sub.isCaseOwner == true)) && ((r.sub.pqcStatusId == 1) || (r.sub.pqcStatusId == 2)) && (r.sub.maxConfirmableQuotesCnt < 5)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"product/product-quote-change/isCaseOwner","field":"isCaseOwner","type":"boolean","input":"radio","operator":"==","value":true}]},{"condition":"OR","rules":[{"id":"product/product-quote-change/pqcStatusId","field":"pqcStatusId","type":"integer","input":"select","operator":"==","value":1},{"id":"product/product-quote-change/pqcStatusId","field":"pqcStatusId","type":"integer","input":"select","operator":"==","value":2}]},{"id":"product/product-quote-change/maxConfirmableQuotesCnt","field":"maxConfirmableQuotesCnt","type":"integer","input":"number","operator":"<","value":5}],"valid":true}',
            'ap_object' => 'product/product-quote-change/obj/product-quote-change',
            'ap_action' => '(createReProtectionQuote)|(createVoluntaryQuote)',
            'ap_action_json' => "[\"createReProtectionQuote\",\"createVoluntaryQuote\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%abac_policy}}', ['AND', ['IN', 'ap_object', [
            'product/product-quote-change/obj/product-quote-change',
        ]], ['IN', 'ap_action', ['(createReProtectionQuote)|(createVoluntaryQuote)']]]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(("admin" in r.sub.env.user.roles) || (r.sub.isCaseOwner == true)) && ((r.sub.pqcStatusId == 1) || (r.sub.pqcStatusId == 2))',
            'ap_subject_json' => '{"condition":"AND","rules":[{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"product/product-quote-change/isCaseOwner","field":"isCaseOwner","type":"boolean","input":"radio","operator":"==","value":true}]},{"condition":"OR","rules":[{"id":"product/product-quote-change/pqcStatusId","field":"pqcStatusId","type":"integer","input":"select","operator":"==","value":1},{"id":"product/product-quote-change/pqcStatusId","field":"pqcStatusId","type":"integer","input":"select","operator":"==","value":2}]}],"valid":true}',
            'ap_object' => 'product/product-quote-change/obj/product-quote-change',
            'ap_action' => '(createReProtectionQuote)|(createVoluntaryQuote)',
            'ap_action_json' => "[\"createReProtectionQuote\",\"createVoluntaryQuote\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
