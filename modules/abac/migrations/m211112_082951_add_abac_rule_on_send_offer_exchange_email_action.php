<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m211112_082951_add_abac_rule_on_send_offer_exchange_email_action
 */
class m211112_082951_add_abac_rule_on_send_offer_exchange_email_action extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.isCaseOwner == true) || ("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"product/product-quote-change/isCaseOwner","field":"isCaseOwner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'product/product-quote-change/obj/product-quote-change',
            'ap_action' => '(sendOfferExchangeEmail)',
            'ap_action_json' => "[\"sendOfferExchangeEmail\"]",
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
            'product/product-quote-change/obj/product-quote-change',
        ]], ['IN', 'ap_action', ['(sendOfferExchangeEmail)']]]);
    }
}
