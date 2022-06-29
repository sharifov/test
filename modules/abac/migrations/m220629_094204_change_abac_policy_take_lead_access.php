<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use yii\db\Migration;

/**
 * Class m220629_094204_change_abac_policy_take_lead_access
 */
class m220629_094204_change_abac_policy_take_lead_access extends Migration
{
    private const AP_SUBJECT = '((((r.sub.status_id == 14) && (r.sub.canTakeByFrequencyMinutes == false)) || ((r.sub.status_id == 1) && ((r.sub.withinPersonalTakeLimits == false) || (r.sub.canTakeByFrequencyMinutes == false))) || ((r.sub.status_id == 15) && (r.sub.canTakeByFrequencyMinutes == false))) && (("agent" in r.sub.env.user.roles) || ("fb_agent" in r.sub.env.user.roles))) || (r.sub.status_id == 4) || (r.sub.status_id == 10) || (r.sub.status_id == 12)';
    private const AP_OBJECT = 'lead/lead/act/take-lead';
    private const AP_ACTION = '(access)';
    private const AP_EFFECT = 0;

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
            ['AND', ['IN', 'ap_object', [self::AP_OBJECT]], ['IN', 'ap_action', [self::AP_ACTION]], ['ap_enabled' => 1],['ap_effect' => 0]]
        );

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => self::AP_SUBJECT,
            'ap_subject_json' => '{"condition":"OR","rules":[{"condition":"AND","rules":[{"condition":"OR","rules":[{"condition":"AND","rules":[{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":14},{"id":"lead/lead/canTakeByFrequencyMinutes","field":"canTakeByFrequencyMinutes","type":"boolean","input":"radio","operator":"==","value":false}],"not":false},{"condition":"AND","rules":[{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":1},{"condition":"OR","rules":[{"id":"lead/lead/withinPersonalTakeLimits","field":"withinPersonalTakeLimits","type":"boolean","input":"radio","operator":"==","value":false},{"id":"lead/lead/canTakeByFrequencyMinutes","field":"canTakeByFrequencyMinutes","type":"boolean","input":"radio","operator":"==","value":false}],"not":false}],"not":false},{"condition":"AND","rules":[{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":15},{"id":"lead/lead/canTakeByFrequencyMinutes","field":"canTakeByFrequencyMinutes","type":"boolean","input":"radio","operator":"==","value":false}],"not":false}],"not":false},{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"agent"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"fb_agent"}],"not":false}],"not":false},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":4},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":10},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":12}],"not":false,"valid":true}',
            'ap_object' => self::AP_OBJECT,
            'ap_action' => self::AP_ACTION,
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => self::AP_EFFECT,
            'ap_title' => 'Take Lead action',
            'ap_sort_order' => 10,
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
            ['AND', ['IN', 'ap_object', [self::AP_OBJECT]], ['IN', 'ap_action', [self::AP_ACTION]], ['ap_enabled' => 1], ['ap_effect' => 0]]
        );

        $this->update(
            '{{%abac_policy}}',
            ['ap_enabled' => 1],
            [
                'AND',
                ['IN', 'ap_object', [self::AP_OBJECT]],
                ['IN', 'ap_action', [self::AP_ACTION]],
                ['ap_enabled' => 0],
                ['ap_effect' => 0]
            ]
        );

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
