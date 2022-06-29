<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use yii\db\Migration;

/**
 * Class m220627_114625_change_abac_permission_call_expert
 */
class m220627_114625_change_abac_permission_call_expert extends Migration
{
    private const AP_SUBJECT = '(r.sub.hasFlightSegment == true) && (r.sub.quoteCount > 0) && (r.sub.leadStatus == 2) && (((r.sub.canMakeCall == true) && (r.sub.callCount > 0)) || (r.sub.canMakeCall == false))';
    private const AP_OBJECT = 'lead/expert_call/act/call';
    private const AP_ACTION = '(access)';
    private const AP_EFFECT = 1;

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
            ['AND', ['IN', 'ap_object', [self::AP_OBJECT]], ['IN', 'ap_action', [self::AP_ACTION]], ['ap_enabled' => 1]]
        );

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => self::AP_SUBJECT,
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"lead/expert_call/hasFlightSegment","field":"hasFlightSegment","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/expert_call/quoteCount","field":"quoteCount","type":"integer","input":"text","operator":">","value":0},{"id":"lead/expert_call/leadStatus","field":"leadStatus","type":"integer","input":"select","operator":"==","value":2},{"condition":"OR","rules":[{"condition":"AND","rules":[{"id":"lead/expert_call/canMakeCall","field":"canMakeCall","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/expert_call/callCount","field":"callCount","type":"integer","input":"text","operator":">","value":0}],"not":false},{"id":"lead/expert_call/canMakeCall","field":"canMakeCall","type":"boolean","input":"radio","operator":"==","value":false}],"not":false}],"not":false,"valid":true}',
            'ap_object' => self::AP_OBJECT,
            'ap_action' => self::AP_ACTION,
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => self::AP_EFFECT,
            'ap_title' => 'Access to create new Expert Call',
            'ap_sort_order' => 50,
            'ap_hash_code' => AbacService::generateHashCode(self::GENERATE_HASH_DATA),
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
            ['AND', ['IN', 'ap_object', [self::AP_OBJECT]], ['IN', 'ap_action', [self::AP_ACTION]], ['ap_enabled' => 1]]
        );

        $this->update(
            '{{%abac_policy}}',
            ['ap_enabled' => 1],
            [
                'AND',
                ['IN', 'ap_object', [self::AP_OBJECT]],
                ['IN', 'ap_action', [self::AP_ACTION]],
                ['ap_enabled' => 0]
            ]
        );

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
