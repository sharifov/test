<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use modules\abac\src\entities\AbacPolicy;
use Yii;
use yii\caching\TagDependency;
use yii\db\Migration;

/**
 * Class m220626_135111_add_abac_permission_for_two_factor_auth
 */
class m220626_135111_add_abac_permission_for_two_factor_auth extends Migration
{
    private const AP_SUBJECT = '(r.sub.env.available == true)';
    private const AP_OBJECT = 'two-factor/two-factor/act/two-factor-auth';
    private const AP_ACTION = '(totpAuth)';
    private const AP_EFFECT = 1;
    private const AP_SUBJECT_JSON = '{"condition":"AND","rules":[{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true}],"not":false,"valid":true}';

    private const GENERATE_HASH_DATA = [
        self::AP_OBJECT,
        self::AP_ACTION,
        self::AP_SUBJECT,
        self::AP_EFFECT
    ];

    private const AP_SUBJECT_OTP = '(r.sub.env.available == true)';
    private const AP_OBJECT_OTP = 'two-factor/two-factor/act/two-factor-auth';
    private const AP_ACTION_OTP = '(otpEmail)';
    private const AP_EFFECT_OTP = 1;
    private const AP_SUBJECT_JSON_OTP = '{"condition":"AND","rules":[{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true}],"not":false,"valid":true}';

    private const GENERATE_HASH_DATA_OTP = [
        self::AP_OBJECT_OTP,
        self::AP_ACTION_OTP,
        self::AP_SUBJECT_OTP,
        self::AP_EFFECT_OTP
    ];


    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => self::AP_SUBJECT,
            'ap_subject_json' => self::AP_SUBJECT_JSON,
            'ap_object' => self::AP_OBJECT,
            'ap_action' => self::AP_ACTION,
            'ap_action_json' => "[\"totpAuth\"]",
            'ap_effect' => self::AP_EFFECT,
            'ap_title' => 'Two Factor Auth with TOTP Auth method',
            'ap_hash_code' => AbacService::generateHashCode(self::GENERATE_HASH_DATA),
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => self::AP_SUBJECT_OTP,
            'ap_subject_json' => self::AP_SUBJECT_JSON_OTP,
            'ap_object' => self::AP_OBJECT_OTP,
            'ap_action' => self::AP_ACTION_OTP,
            'ap_action_json' => "[\"otpEmail\"]",
            'ap_effect' => self::AP_EFFECT_OTP,
            'ap_title' => 'Two Factor Auth with OTP Email method',
            'ap_hash_code' => AbacService::generateHashCode(self::GENERATE_HASH_DATA_OTP),
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);
        Yii::$app->abac->invalidatePolicyCache();
//        $cacheTagDependency = Yii::$app->abac->getCacheTagDependency();
//        if ($cacheTagDependency) {
//            TagDependency::invalidate(Yii::$app->cache, $cacheTagDependency);
//        }
//        TagDependency::invalidate(Yii::$app->cache, [AbacPolicy::CACHE_KEY]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (AbacPolicy::deleteAll(['AND', ['IN', 'ap_object', ['two-factor/two-factor/act/two-factor-auth',]], ['IN', 'ap_action', ['(otpEmail)', '(totpAuth)']]])) {
            Yii::$app->abac->invalidatePolicyCache();
//            $cacheTagDependency = Yii::$app->abac->getCacheTagDependency();
//            if ($cacheTagDependency) {
//                TagDependency::invalidate(Yii::$app->cache, $cacheTagDependency);
//            }
//            TagDependency::invalidate(Yii::$app->cache, [AbacPolicy::CACHE_KEY]);
        }
    }
}
