<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use yii\db\Migration;

/**
 * Class m220707_065246_change_abac_policy_2fa_access_permissions
 */
class m220707_065246_change_abac_policy_2fa_access_permissions extends Migration
{
    private const AP_SUBJECT_ALLOW = '(r.sub.env.available == true)';
    private const AP_OBJECT = 'two-factor/two-factor/act/two-factor-auth';
    private const AP_ACTION = '(access)';
    private const AP_EFFECT_ALLOW = 1;
    private const AP_SUBJECT_JSON_ALLOW = '{"condition":"AND","rules":[{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true}],"not":false,"valid":true}';

    private const GENERATE_HASH_DATA_ALLOW = [
        self::AP_OBJECT,
        self::AP_ACTION,
        self::AP_SUBJECT_ALLOW,
        self::AP_SUBJECT_JSON_ALLOW,
        self::AP_EFFECT_ALLOW,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update(
            '{{%abac_policy}}',
            ['ap_enabled' => 0],
            ['AND', ['IN', 'ap_object', [self::AP_OBJECT]], ['IN', 'ap_action', [self::AP_ACTION]], ['ap_effect' => 1]]
        );

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => self::AP_SUBJECT_ALLOW,
            'ap_subject_json' => self::AP_SUBJECT_JSON_ALLOW,
            'ap_object' => self::AP_OBJECT,
            'ap_action' => self::AP_ACTION,
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => self::AP_EFFECT_ALLOW,
            'ap_title' => 'Two Factor Access Permissions',
            'ap_hash_code' => AbacService::generateHashCode(self::GENERATE_HASH_DATA_ALLOW),
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete(
            '{{%abac_policy}}',
            ['AND', ['IN', 'ap_object', [self::AP_OBJECT]], ['IN', 'ap_action', [self::AP_ACTION]], ['ap_enabled' => 1], ['ap_effect' => 1]]
        );

        $this->update(
            '{{%abac_policy}}',
            ['ap_enabled' => 1],
            [
                'AND',
                ['IN', 'ap_object', [self::AP_OBJECT]],
                ['IN', 'ap_action', [self::AP_ACTION]],
                ['ap_enabled' => 0],
                ['ap_effect' => 1]
            ]
        );

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
