<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use yii\db\Migration;

/**
 * Class m220704_142848_update_abac_permission_user_shift_calendar
 */
class m220704_142848_update_abac_permission_user_shift_calendar extends Migration
{
    private const AP_SUBJECT = '("admin" in r.sub.env.user.roles)';
    private const AP_OBJECT = 'shift/shift/obj/user_shift_calendar';
    private const AP_ACTION = '(access)';
    private const AP_EFFECT = 1;
    private const AP_SUBJECT_JSON = '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}';

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
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => self::AP_EFFECT,
            'ap_title' => 'Access to shift calendar page',
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
