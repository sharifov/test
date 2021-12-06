<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m211203_123349_add_abac_rule_on_send_voluntary_refound_email_action
 */
class m211203_123349_add_abac_rule_on_send_voluntary_refound_email_action extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(("admin" in r.sub.env.user.roles) || (r.sub.isCaseOwner == true)) && (r.sub.pqrStatusId == 1) && (r.sub.hasPqrActive == false) && (r.sub.hasPqcActive == false)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"product/product-quote-refund/isCaseOwner","field":"isCaseOwner","type":"boolean","input":"radio","operator":"==","value":true}]},{"id":"product/product-quote-refund/pqrStatusId","field":"pqrStatusId","type":"integer","input":"select","operator":"==","value":1},{"id":"product/product-quote-refund/hasPqrActive","field":"hasPqrActive","type":"boolean","input":"radio","operator":"==","value":false},{"id":"product/product-quote-refund/hasPqcActive","field":"hasPqcActive","type":"boolean","input":"radio","operator":"==","value":false}],"valid":true}',
            'ap_object' => 'product/product-quote-refund/obj/product-quote-refund',
            'ap_action' => '(sendVoluntaryRefundEmail)',
            'ap_action_json' => "[\"sendVoluntaryRefundEmail\"]",
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
        $this->delete('{{%abac_policy}}', ['AND', ['IN', 'ap_object', [
            'product/product-quote-refund/obj/product-quote-refund',
        ]], ['IN', 'ap_action', ['(sendVoluntaryRefundEmail)']]]);

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
