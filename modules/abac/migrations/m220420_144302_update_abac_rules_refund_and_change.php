<?php

namespace modules\abac\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m220420_144302_update_abac_rules_refund_and_change
 */
class m220420_144302_update_abac_rules_refund_and_change extends Migration
{
    public const TABLE = '{{%abac_policy}}';

    /**
     * @return void
     */
    public function safeUp(): void
    {
        $this->update(
            self::TABLE,
            ['ap_enabled' => 0],
            [
                'AND',
                ['ap_object' => 'product/product-quote-refund/obj/product-quote-refund'],
                ['ap_action' => '(sendVoluntaryRefundEmail)']
            ]
        );

        $this->update(
            self::TABLE,
            ['ap_enabled' => 0],
            [
                'AND',
                ['ap_object' => 'product/product-quote/obj/product-quote'],
                ['ap_action' => '(createChange)|(createVoluntaryRefundQuote)']
            ]
        );

        $this->insert(
            self::TABLE,
            [
                'ap_rule_type' => 'p',
                'ap_subject' => '(("admin" in r.sub.env.user.roles) || ("support_senior" in r.sub.env.user.roles) || ((r.sub.isCaseOwner == true) && (("ex_agent" in r.sub.env.user.roles) || ("exchange_agent_with_inbox" in r.sub.env.user.roles))) || ("schd_super" in r.sub.env.user.roles) || ((r.sub.isCaseOwner == true) && ("schd_agent" in r.sub.env.user.roles)) || (("ex_super" in r.sub.env.user.roles))) && (r.sub.pqrStatusId == 1) && (r.sub.hasPqrActive == false) && (r.sub.hasPqcAccepted == false)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"support_senior"},{"condition":"AND","rules":[{"id":"product/product-quote-refund/isCaseOwner","field":"isCaseOwner","type":"boolean","input":"radio","operator":"==","value":true},{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"ex_agent"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"exchange_agent_with_inbox"}]}]},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"schd_super"},{"condition":"AND","rules":[{"id":"product/product-quote-refund/isCaseOwner","field":"isCaseOwner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"schd_agent"}]},{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"ex_super"}]}]},{"id":"product/product-quote-refund/pqrStatusId","field":"pqrStatusId","type":"integer","input":"select","operator":"==","value":1},{"id":"product/product-quote-refund/hasPqrActive","field":"hasPqrActive","type":"boolean","input":"radio","operator":"==","value":false},{"id":"product/product-quote-refund/hasPqcAccepted","field":"hasPqcAccepted","type":"boolean","input":"radio","operator":"==","value":false}],"valid":true}',
                'ap_object' => 'product/product-quote-refund/obj/product-quote-refund',
                'ap_action' => '(sendVoluntaryRefundEmail)',
                'ap_action_json' => json_encode(['sendVoluntaryRefundEmail']),
                'ap_effect' => 1,
                'ap_title' => '',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]
        );

        $this->insert(
            self::TABLE,
            [
                'ap_rule_type' => 'p',
                'ap_subject' => '((("admin" in r.sub.env.user.roles) || ("support_senior" in r.sub.env.user.roles)) || ((r.sub.isOrderOwner == true) && (("ex_agent" in r.sub.env.user.roles) || ("ex_super" in r.sub.env.user.roles) || ("exchange_senior" in r.sub.env.user.roles) || ("schd_agent" in r.sub.env.user.roles) || ("schd_super" in r.sub.env.user.roles)))) && (r.sub.isPqChangeable == true) && (r.sub.hasPqrActive == false) && (r.sub.hasPqcAccepted == false)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"condition":"OR","rules":[{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"support_senior"}]},{"condition":"AND","rules":[{"id":"product/product-quote/isOrderOwner","field":"isOrderOwner","type":"boolean","input":"radio","operator":"==","value":true},{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"ex_agent"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"ex_super"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"exchange_senior"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"schd_agent"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"schd_super"}]}]}]},{"id":"product/product-quote/isPqChangeable","field":"isPqChangeable","type":"boolean","input":"radio","operator":"==","value":true},{"id":"product/product-quote/hasPqrActive","field":"hasPqrActive","type":"boolean","input":"radio","operator":"==","value":false},{"id":"product/product-quote/hasPqcAccepted","field":"hasPqcAccepted","type":"boolean","input":"radio","operator":"==","value":false}],"valid":true}',
                'ap_object' => 'product/product-quote/obj/product-quote',
                'ap_action' => '(createVoluntaryRefundQuote)',
                'ap_action_json' => json_encode(['createVoluntaryRefundQuote']),
                'ap_effect' => 1,
                'ap_title' => '',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]
        );

        $this->insert(
            self::TABLE,
            [
                'ap_rule_type' => 'p',
                'ap_subject' => '((("admin" in r.sub.env.user.roles) || ("support_senior" in r.sub.env.user.roles)) || ((r.sub.isOrderOwner == true) && (("ex_agent" in r.sub.env.user.roles) || ("ex_super" in r.sub.env.user.roles) || ("exchange_senior" in r.sub.env.user.roles) || ("schd_agent" in r.sub.env.user.roles) || ("schd_super" in r.sub.env.user.roles)))) && (r.sub.isPqChangeable == true) && (r.sub.hasPqcActive == false) && (r.sub.hasPqrAccepted == false)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"condition":"OR","rules":[{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"support_senior"}]},{"condition":"AND","rules":[{"id":"product/product-quote/isOrderOwner","field":"isOrderOwner","type":"boolean","input":"radio","operator":"==","value":true},{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"ex_agent"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"ex_super"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"exchange_senior"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"schd_agent"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"schd_super"}]}]}]},{"id":"product/product-quote/isPqChangeable","field":"isPqChangeable","type":"boolean","input":"radio","operator":"==","value":true},{"id":"product/product-quote/hasPqcActive","field":"hasPqcActive","type":"boolean","input":"radio","operator":"==","value":false},{"id":"product/product-quote/hasPqrAccepted","field":"hasPqrAccepted","type":"boolean","input":"radio","operator":"==","value":false}],"valid":true}',
                'ap_object' => 'product/product-quote/obj/product-quote',
                'ap_action' => '(createChange)',
                'ap_action_json' => json_encode(['createChange']),
                'ap_effect' => 1,
                'ap_title' => '',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]
        );

        Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * @return void
     */
    public function safeDown(): void
    {
        // nothing
    }
}
