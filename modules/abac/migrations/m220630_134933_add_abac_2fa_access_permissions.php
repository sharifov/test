<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220630_134933_add_abac_2fa_access_permissions
 */
class m220630_134933_add_abac_2fa_access_permissions extends Migration
{
    private const AP_SUBJECT_ALLOW = '(env.available == true)';
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
    private const AP_EFFECT_DENY = 0;

    private const AP_SUBJECT_DENY = '("superadmin" in env.user.roles)';
    private const AP_SUBJECT_JSON_DENY = '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"superadmin"}],"not":false,"valid":true}';
    private const GENERATE_HASH_DATA_DENY = [
        self::AP_OBJECT,
        self::AP_ACTION,
        self::AP_SUBJECT_DENY,
        self::AP_SUBJECT_JSON_DENY,
        self::AP_EFFECT_DENY
    ];


    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $isInvalidate = false;
        if (!AbacPolicy::find()->where(['ap_object' => self::AP_OBJECT])->andWhere(['ap_action' => self::AP_ACTION])->exists()) {
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

//            $this->insert('{{%abac_policy}}', [
//                'ap_rule_type' => 'p',
//                'ap_subject' => self::AP_SUBJECT_DENY,
//                'ap_subject_json' => self::AP_SUBJECT_JSON_DENY,
//                'ap_object' => self::AP_OBJECT,
//                'ap_action' => self::AP_ACTION,
//                'ap_action_json' => "[\"access\"]",
//                'ap_effect' => self::AP_EFFECT_DENY,
//                'ap_title' => 'Two Factor Access Permissions For Superuser Admin',
//                'ap_hash_code' => AbacService::generateHashCode(self::GENERATE_HASH_DATA_DENY),
//                'ap_sort_order' => 10,
//                'ap_enabled' => 1,
//                'ap_created_dt' => date('Y-m-d H:i:s'),
//            ]);
            $isInvalidate = true;
        }

        if ($isInvalidate) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $isInvalidate = false;
        if (AbacPolicy::deleteAll(['ap_object' => self::AP_OBJECT, 'ap_action' => self::AP_ACTION])) {
            $isInvalidate = true;
        }
        if ($isInvalidate) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
