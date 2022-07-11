<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use src\rbac\RbacMoveToAbacService;
use yii\db\Migration;

/**
 * Class m220630_101840_add_abac_policy_advanced_search_lead
 */
class m220630_101840_add_abac_policy_advanced_search_lead extends Migration
{
    private const AP_SUBJECT_ALLOW = '(r.sub.env.available == true)';
    private const AP_SUBJECT_DENY = '("agent" in r.sub.env.user.roles) || ("business_agent" in r.sub.env.user.roles) || ("ex_agent" in r.sub.env.user.roles) || ("exchange_agent_with_inbox" in r.sub.env.user.roles) || ("fb_agent" in r.sub.env.user.roles) || ("manager_crm" in r.sub.env.user.roles) || ("qa_developer" in r.sub.env.user.roles) || ("schd_agent" in r.sub.env.user.roles)';
    private const AP_OBJECT = 'lead/lead/obj/advanced_search';
    private const AP_ACTION = '(access)';
    private const AP_EFFECT_ALLOW = 1;
    private const AP_EFFECT_DENY = 0;

    private string $permissionName = 'isAgent';

    private const GENERATE_HASH_DATA_ALLOW = [
        self::AP_OBJECT,
        self::AP_ACTION,
        self::AP_SUBJECT_ALLOW,
        self::AP_EFFECT_ALLOW
    ];

    private const GENERATE_HASH_DATA_DENY = [
        self::AP_OBJECT,
        self::AP_ACTION,
        self::AP_SUBJECT_DENY,
        self::AP_EFFECT_DENY
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete(
            '{{%abac_policy}}',
            ['AND', ['IN', 'ap_object', ['lead/search/simple_search']], ['IN', 'ap_action', [self::AP_ACTION]]]
        );

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => self::AP_SUBJECT_ALLOW,
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true}],"not":false,"valid":true}',
            'ap_object' => self::AP_OBJECT,
            'ap_action' => self::AP_ACTION,
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => self::AP_EFFECT_ALLOW,
            'ap_title' => 'Access to Advanced search Lead',
            'ap_hash_code' => AbacService::generateHashCode(self::GENERATE_HASH_DATA_ALLOW),
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => self::AP_SUBJECT_DENY,
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"agent"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"business_agent"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"ex_agent"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"exchange_agent_with_inbox"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"fb_agent"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"manager_crm"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"qa_developer"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"schd_agent"}],"not":false,"valid":true}',
            'ap_object' => self::AP_OBJECT,
            'ap_action' => self::AP_ACTION,
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => self::AP_EFFECT_DENY,
            'ap_title' => 'Access to Advanced search Lead',
            'ap_hash_code' => AbacService::generateHashCode(self::GENERATE_HASH_DATA_DENY),
            'ap_sort_order' => 20,
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
        $service = new RbacMoveToAbacService($this->permissionName);

        if (!empty($service->getApSubject())) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => $service->getApSubject(),
                'ap_subject_json' => $service->getApSubjectJson(),
                'ap_object' => 'lead/search/simple_search',
                'ap_action' => '(access)',
                'ap_action_json' => "[\"access\"]",
                'ap_effect' => 1,
                'ap_title' => 'Access to Simple Search Lead',
                'ap_sort_order' => 50,
                'ap_hash_code' => AbacService::generateHashCode(['lead/search/simple_search', '(access)', $service->getApSubject(), 1]),
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '(r.sub.env.available == false)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":false}],"valid":true}',
                'ap_object' => 'lead/search/simple_search',
                'ap_action' => '(access)',
                'ap_action_json' => "[\"access\"]",
                'ap_hash_code' => AbacService::generateHashCode(['lead/search/simple_search', '(access)', '(r.sub.env.available == false)', 1]),
                'ap_effect' => 1,
                'ap_title' => 'Access to Simple Search Lead',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->delete(
            '{{%abac_policy}}',
            ['AND', ['IN', 'ap_object', [self::AP_OBJECT]], ['IN', 'ap_action', [self::AP_ACTION]], ['ap_enabled' => 1]]
        );
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
