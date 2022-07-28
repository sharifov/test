<?php

declare(strict_types=1);

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use Yii;
use yii\db\Migration;

/**
 * Class m220721_143422_abac_correct_sendOfferExchangeEmail_action
 */
class m220721_143422_abac_correct_sendOfferExchangeEmail_action extends Migration
{
    protected const TABLE = '{{%abac_policy}}';
    protected const APP_OBJECT = 'product/product-quote-change/obj/product-quote-change';
    protected const APP_ACTION = '(sendOfferExchangeEmail)';
    protected const APP_ACTION_JSON = ['sendOfferExchangeEmail'];


    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->reset();

        $apSubject = '(r.sub.isPqChangeable == true) && (("admin" in r.sub.env.user.roles) || ("support_senior" in r.sub.env.user.roles) || ((("ex_super" in r.sub.env.user.roles) || ("ex_agent" in r.sub.env.user.roles) || ("exchange_agent_with_inbox" in r.sub.env.user.roles) || ((r.sub.isCaseOwner == true)))) || ((r.sub.isCaseOwner == true) && ("schd_agent" in r.sub.env.user.roles)) || ("schd_super" in r.sub.env.user.roles)) && ((r.sub.pqcStatusId == 1) || (r.sub.pqcStatusId == 2)) && (r.sub.hasPqNew == true)';
        $this->insert(
            self::TABLE,
            [
                'ap_rule_type' => 'p',
                'ap_subject' => $apSubject,
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"product/product-quote-change/isPqChangeable","field":"isPqChangeable","type":"boolean","input":"radio","operator":"==","value":true},{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"support_senior"},{"condition":"AND","rules":[{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"ex_super"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"ex_agent"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"exchange_agent_with_inbox"},{"condition":"AND","rules":[{"id":"product/product-quote-change/isCaseOwner","field":"isCaseOwner","type":"boolean","input":"radio","operator":"==","value":true}],"not":false}],"not":false}],"not":false},{"condition":"AND","rules":[{"id":"product/product-quote-change/isCaseOwner","field":"isCaseOwner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"schd_agent"}],"not":false},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"schd_super"}],"not":false},{"condition":"OR","rules":[{"id":"product/product-quote-change/pqcStatusId","field":"pqcStatusId","type":"integer","input":"select","operator":"==","value":1},{"id":"product/product-quote-change/pqcStatusId","field":"pqcStatusId","type":"integer","input":"select","operator":"==","value":2}],"not":false},{"id":"product/product-quote-change/hasPqNew","field":"hasPqNew","type":"boolean","input":"radio","operator":"==","value":true}],"not":false,"valid":true}',
                'ap_object' => self::APP_OBJECT,
                'ap_action' => self::APP_ACTION,
                'ap_action_json' => json_encode(self::APP_ACTION_JSON),
                'ap_effect' => 1,
                'ap_title' => '',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_hash_code' => AbacService::generateHashCode([
                    self::APP_OBJECT,
                    self::APP_ACTION,
                    $apSubject,
                    1,
                ]),
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]
        );

        Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->reset();

        $apSubject = '(r.sub.isPqChangeable == true) && (("admin" in r.sub.env.user.roles) || ("support_senior" in r.sub.env.user.roles) || ((("ex_super" in r.sub.env.user.roles) || ("ex_agent" in r.sub.env.user.roles) || ("exchange_agent_with_inbox" in r.sub.env.user.roles) || ((r.sub.isCaseOwner == true)))) || ((r.sub.isCaseOwner == true) && ("schd_agent" in r.sub.env.user.roles)) || ("schd_super" in r.sub.env.user.roles)) && (r.sub.pqcStatusId != 3) && (r.sub.pqcStatusId != 8)';
        $this->insert(
            self::TABLE,
            [
                'ap_rule_type' => 'p',
                'ap_subject' => $apSubject,
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"product/product-quote-change/isPqChangeable","field":"isPqChangeable","type":"boolean","input":"radio","operator":"==","value":true},{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"support_senior"},{"condition":"AND","rules":[{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"ex_super"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"ex_agent"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"exchange_agent_with_inbox"},{"condition":"AND","rules":[{"id":"product/product-quote-change/isCaseOwner","field":"isCaseOwner","type":"boolean","input":"radio","operator":"==","value":true}],"not":false}],"not":false}],"not":false},{"condition":"AND","rules":[{"id":"product/product-quote-change/isCaseOwner","field":"isCaseOwner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"schd_agent"}],"not":false},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"schd_super"}],"not":false},{"id":"product/product-quote-change/pqcStatusId","field":"pqcStatusId","type":"integer","input":"select","operator":"!=","value":3},{"id":"product/product-quote-change/pqcStatusId","field":"pqcStatusId","type":"integer","input":"select","operator":"!=","value":8}],"not":false,"valid":true}',
                'ap_object' => self::APP_OBJECT,
                'ap_action' => self::APP_ACTION,
                'ap_action_json' => json_encode(self::APP_ACTION_JSON),
                'ap_effect' => 1,
                'ap_title' => '',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_hash_code' => AbacService::generateHashCode([
                    self::APP_OBJECT,
                    self::APP_ACTION,
                    $apSubject,
                    1,
                ]),
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]
        );

        Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * @return void
     */
    protected function reset(): void
    {
        $this->delete(self::TABLE, [
            'AND',
            ['ap_object' => self::APP_OBJECT],
            ['ap_action' => self::APP_ACTION],
        ]);
    }
}
