<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220701_054737_add_abac_permission_for_take_lead_from_chat
 */
class m220701_054737_add_abac_permission_for_take_lead_from_chat extends Migration
{
    private const AP_SUBJECT = '(("agent" in r.sub.env.user.roles) && (((r.sub.status_id == 14) && (r.sub.canTakeByFrequencyMinutes == false)) || ((r.sub.status_id == 1) && ((r.sub.withinPersonalTakeLimits == false) || (r.sub.canTakeByFrequencyMinutes == false))))) || (r.sub.status_id == 4) || (r.sub.status_id == 10) || (r.sub.status_id == 12)';
    private const AP_OBJECT = 'lead/lead/act/take-lead-from-chat';
    private const AP_ACTION = '(access)';
    private const AP_EFFECT = 0;

    private const AP_SUBJECT_ALLOW = '(r.sub.is_owner == false) && (("admin" in r.sub.env.user.roles) || ((r.sub.isInProject == true) && (r.sub.isInDepartment == true)))';
    private const AP_EFFECT_ALLOW = 1;

    private const GENERATE_HASH_DATA = [
        self::AP_OBJECT,
        self::AP_ACTION,
        self::AP_SUBJECT,
        self::AP_EFFECT
    ];

    private const GENERATE_HASH_DATA_ALLOW = [
        self::AP_OBJECT,
        self::AP_ACTION,
        self::AP_SUBJECT_ALLOW,
        self::AP_EFFECT_ALLOW
    ];


    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => self::AP_SUBJECT,
            'ap_subject_json' => '{"condition":"OR","rules":[{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"agent"},{"condition":"OR","rules":[{"condition":"AND","rules":[{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":14},{"id":"lead/lead/canTakeByFrequencyMinutes","field":"canTakeByFrequencyMinutes","type":"boolean","input":"radio","operator":"==","value":false}],"not":false},{"condition":"AND","rules":[{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":1},{"condition":"OR","rules":[{"id":"lead/lead/withinPersonalTakeLimits","field":"withinPersonalTakeLimits","type":"boolean","input":"radio","operator":"==","value":false},{"id":"lead/lead/canTakeByFrequencyMinutes","field":"canTakeByFrequencyMinutes","type":"boolean","input":"radio","operator":"==","value":false}],"not":false}],"not":false}],"not":false}],"not":false},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":4},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":10},{"id":"lead/lead/status_id","field":"status_id","type":"integer","input":"select","operator":"==","value":12}],"not":false,"valid":true}',
            'ap_object' => self::AP_OBJECT,
            'ap_action' => self::AP_ACTION,
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => self::AP_EFFECT,
            'ap_title' => 'Take Lead action From Chat',
            'ap_sort_order' => 10,
            'ap_hash_code' => AbacService::generateHashCode(self::GENERATE_HASH_DATA),
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => self::AP_SUBJECT_ALLOW,
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":false},{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"condition":"AND","rules":[{"id":"lead/lead/isInProject","field":"isInProject","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/lead/isInDepartment","field":"isInDepartment","type":"boolean","input":"radio","operator":"==","value":true}],"not":false}],"not":false}],"not":false,"valid":true}',
            'ap_object' => self::AP_OBJECT,
            'ap_action' => self::AP_ACTION,
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => self::AP_EFFECT_ALLOW,
            'ap_title' => 'Take Lead action From Chat',
            'ap_sort_order' => 50,
            'ap_hash_code' => AbacService::generateHashCode(self::GENERATE_HASH_DATA_ALLOW),
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
        if (AbacPolicy::deleteAll(['AND', ['IN', 'ap_object', [self::AP_OBJECT]], ['IN', 'ap_action', [self::AP_ACTION]]])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
