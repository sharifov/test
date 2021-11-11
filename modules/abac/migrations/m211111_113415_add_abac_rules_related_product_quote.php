<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m211111_113415_add_abac_rules_related_product_quote
 */
class m211111_113415_add_abac_rules_related_product_quote extends Migration
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
            'ap_object' => 'product/product-quote/obj/related-product-quote',
            'ap_action' => '(accessDetails)|(accessDifference)',
            'ap_action_json' => "[\"accessDetails\",\"accessDifference\"]",
            'ap_effect' => 1,
            'ap_title' => 'Related Product Quote',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(("admin" in r.sub.env.user.roles) || (r.sub.isCaseOwner == true)) && ((r.sub.pqcStatusId == 1) || (r.sub.pqcStatusId == 2) || (r.sub.pqcStatusId == 5))',
            'ap_subject_json' => '{"condition":"AND","rules":[{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"product/product-quote/isCaseOwner","field":"isCaseOwner","type":"boolean","input":"radio","operator":"==","value":true}]},{"condition":"OR","rules":[{"id":"product/product-quote/pqcStatusId","field":"pqcStatusId","type":"integer","input":"select","operator":"==","value":1},{"id":"product/product-quote/pqcStatusId","field":"pqcStatusId","type":"integer","input":"select","operator":"==","value":2},{"id":"product/product-quote/pqcStatusId","field":"pqcStatusId","type":"integer","input":"select","operator":"==","value":5}]}],"valid":true}',
            'ap_object' => 'product/product-quote/obj/related-product-quote',
            'ap_action' => '(sendSCEmail)|(setConfirmed)|(setRefunded)|(setRecommended)|(setDecline)',
            'ap_action_json' => "[\"sendSCEmail\",\"setConfirmed\",\"setRefunded\",\"setRecommended\",\"setDecline\"]",
            'ap_effect' => 1,
            'ap_title' => 'Related Product Quote',
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
        $this->delete('{{%abac_policy}}', ['AND', ['IN', 'ap_object', [
            'product/product-quote-refund/obj/product-quote-refund',
        ]], ['IN', 'ap_action', ['(update)']]]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
