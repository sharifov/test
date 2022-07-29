<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use modules\abac\src\entities\AbacMigration;

/**
 * Class m220727_081237_change_abac_permission_user_conversion_create
 */
class m220727_081237_change_abac_permission_user_conversion_create extends AbacMigration
{
    private const AP_SUBJECT = '(r.sub.closeReason == "booked_with_another_agent") || (r.sub.closeReason == "canceled_trip") || (r.sub.closeReason == "client_asked_not_to_be_contacted_again") || (r.sub.closeReason == "competitor_has_a_better_contract") || (r.sub.closeReason == "proper_follow_up_done") || (r.sub.closeReason == "purchased_elsewhere") || (r.sub.closeReason == "travel_dates_passed")';
    private const AP_OBJECT = 'lead/lead/obj/user-conversion';
    private const AP_ACTION = '(create)';
    private const AP_EFFECT = 1;
    private const AP_SUBJECT_JSON = '{"condition":"OR","rules":[{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"booked_with_another_agent"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"canceled_trip"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"client_asked_not_to_be_contacted_again"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"competitor_has_a_better_contract"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"proper_follow_up_done"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"purchased_elsewhere"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"travel_dates_passed"}],"not":false,"valid":true}';

    private const GENERATE_HASH_DATA = [
        self::AP_OBJECT,
        self::AP_ACTION,
        self::AP_SUBJECT,
        self::AP_EFFECT
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update(
            '{{%abac_policy}}',
            ['ap_enabled' => 0],
            ['AND', ['IN', 'ap_object', [self::AP_OBJECT]], ['IN', 'ap_action', [self::AP_ACTION]], ['ap_enabled' => 1],['ap_effect' => self::AP_EFFECT]]
        );

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => self::AP_SUBJECT,
            'ap_subject_json' => self::AP_SUBJECT_JSON,
            'ap_object' => self::AP_OBJECT,
            'ap_action' => self::AP_ACTION,
            'ap_action_json' => "[\"create\"]",
            'ap_effect' => self::AP_EFFECT,
            'ap_title' => 'Permission to create user conversion',
            'ap_hash_code' => AbacService::generateHashCode(self::GENERATE_HASH_DATA),
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
        $this->delete(
            '{{%abac_policy}}',
            ['AND', ['IN', 'ap_object', [self::AP_OBJECT]], ['IN', 'ap_action', [self::AP_ACTION]], ['ap_enabled' => 1], ['ap_effect' => self::AP_EFFECT]]
        );

        $this->update(
            '{{%abac_policy}}',
            ['ap_enabled' => 1],
            [
                'AND',
                ['IN', 'ap_object', [self::AP_OBJECT]],
                ['IN', 'ap_action', [self::AP_ACTION]],
                ['ap_enabled' => 0],
                ['ap_effect' => self::AP_EFFECT]
            ]
        );

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
