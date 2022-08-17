<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use modules\abac\src\entities\AbacMigration;
use modules\abac\src\entities\AbacPolicy;
use Yii;

/**
 * Class m220817_060444_add_abac_policy_email_template_type_by_roles
 */
class m220817_060444_add_abac_policy_email_template_type_by_roles extends AbacMigration
{
    private const AP_OBJECT = 'email/obj/email-template-type';
    private const AP_ACTION = '(access)';

    private const AP_SUBJECT_ALLOW = '(r.sub.env.available == true)';
    private const AP_EFFECT_ALLOW = 1;
    private const AP_SUBJECT_JSON_ALLOW = '{"condition":"AND","rules":[{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true}],"not":false,"valid":true}';

    private const AP_SUBJECT_DENY = '(r.sub.template_key == "alternative_follow_up") && (("alternative_agent" not in r.sub.env.user.roles) && ("fb_agent" not in r.sub.env.user.roles) && ("admin" not in r.sub.env.user.roles))';
    private const AP_EFFECT_DENY = 0;
    private const AP_SUBJECT_JSON_DENY = '{"condition":"AND","rules":[{"id":"email/template_key","field":"template_key","type":"string","input":"select","operator":"==","value":"alternative_follow_up"},{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"not_in_array","value":"alternative_agent"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"not_in_array","value":"fb_agent"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"not_in_array","value":"admin"}],"not":false}],"not":false,"valid":true}';

    private const GENERATE_HASH_DATA_ALLOW = [
        self::AP_OBJECT,
        self::AP_ACTION,
        self::AP_SUBJECT_ALLOW,
        self::AP_EFFECT_ALLOW,
    ];

    private const GENERATE_HASH_DATA_DENY = [
        self::AP_OBJECT,
        self::AP_ACTION,
        self::AP_SUBJECT_DENY,
        self::AP_EFFECT_DENY,
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => self::AP_SUBJECT_ALLOW,
            'ap_subject_json' => self::AP_SUBJECT_JSON_ALLOW,
            'ap_object' => self::AP_OBJECT,
            'ap_action' => self::AP_ACTION,
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => self::AP_EFFECT_ALLOW,
            'ap_title' => 'Access to email template',
            'ap_hash_code' => AbacService::generateHashCode(self::GENERATE_HASH_DATA_ALLOW),
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);


        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => self::AP_SUBJECT_DENY,
            'ap_subject_json' => self::AP_SUBJECT_JSON_DENY,
            'ap_object' => self::AP_OBJECT,
            'ap_action' => self::AP_ACTION,
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => self::AP_EFFECT_DENY,
            'ap_title' => 'Access to email template',
            'ap_hash_code' => AbacService::generateHashCode(self::GENERATE_HASH_DATA_DENY),
            'ap_sort_order' => 10,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (AbacPolicy::deleteAll(['ap_object' => self::AP_OBJECT, 'ap_action' => self::AP_ACTION])) {
            Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
