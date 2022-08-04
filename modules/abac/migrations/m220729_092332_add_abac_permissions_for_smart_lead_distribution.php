<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use modules\abac\src\entities\AbacMigration;

/**
 * Class m220729_092332_add_abac_permissions_for_smart_lead_distribution
 */
class m220729_092332_add_abac_permissions_for_smart_lead_distribution extends AbacMigration
{
    private const POLICY_LIST = [
        [
            'ap_subject' => '("admin" in r.sub.env.user.roles) || ((r.sub.env.user.skill in ["3"])) || (r.sub.env.user.skill not in ["1","2","3"])',
            'ap_object' => 'smartLeadDistribution/business_leads/first_category',
            'ap_action' => '(access)',
            'ap_effect' => 1,
            'ap_title' => 'Access to first category business lead',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"condition":"AND","rules":[{"id":"env_user_skill","field":"env.user.skill","type":"string","input":"select","operator":"in","value":["3"]}],"not":false},{"id":"env_user_skill","field":"env.user.skill","type":"string","input":"select","operator":"not_in","value":["1","2","3"]}],"not":false,"valid":true}',
        ],
        [
            'ap_subject' => '("admin" in r.sub.env.user.roles) || ((r.sub.env.user.skill in ["2","3"]) || ((r.sub.env.user.skill in ["1"]) && (r.sub.quantity_third_category <= 0))) || (r.sub.env.user.skill not in ["1","2","3"])',
            'ap_object' => 'smartLeadDistribution/business_leads/second_category',
            'ap_action' => '(access)',
            'ap_effect' => 1,
            'ap_title' => 'Access to second category business lead',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"condition":"OR","rules":[{"id":"env_user_skill","field":"env.user.skill","type":"string","input":"select","operator":"in","value":["2","3"]},{"condition":"AND","rules":[{"id":"env_user_skill","field":"env.user.skill","type":"string","input":"select","operator":"in","value":["1"]},{"id":"smartLeadDistribution/quantity_third_category","field":"quantity_third_category","type":"integer","input":"number","operator":"<=","value":0}],"not":false}],"not":false},{"id":"env_user_skill","field":"env.user.skill","type":"string","input":"select","operator":"not_in","value":["1","2","3"]}],"not":false,"valid":true}',
        ],
        [
            'ap_subject' => '("admin" in r.sub.env.user.roles) || (r.sub.env.user.skill in ["1","2","3"]) || (r.sub.env.user.skill not in ["1","2","3"])',
            'ap_object' => 'smartLeadDistribution/business_leads/third_category',
            'ap_action' => '(access)',
            'ap_effect' => 1,
            'ap_title' => 'Access to third category business lead',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"env_user_skill","field":"env.user.skill","type":"string","input":"select","operator":"in","value":["1","2","3"]},{"id":"env_user_skill","field":"env.user.skill","type":"string","input":"select","operator":"not_in","value":["1","2","3"]}],"not":false,"valid":true}',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /** @var array{ap_subject: string, ap_object: string, ap_action: string, ap_effect: integer, ap_title: string, ap_subject_json: string} $policy */
        foreach (self::POLICY_LIST as $policy) {
            $hashArray = [
                $policy['ap_object'],
                $policy['ap_action'],
                $policy['ap_subject'],
                $policy['ap_effect'],
            ];

            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => $policy['ap_subject'],
                'ap_subject_json' => $policy['ap_subject_json'],
                'ap_object' => $policy['ap_object'],
                'ap_action' => $policy['ap_action'],
                'ap_action_json' => "[\"access\"]",
                'ap_effect' => $policy['ap_effect'],
                'ap_title' => $policy['ap_title'],
                'ap_hash_code' => AbacService::generateHashCode($hashArray),
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
        }

        \Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        /** @var array{ap_subject: string, ap_object: string, ap_action: string, ap_effect: integer, ap_title: string, ap_subject_json: string} $policy */
        foreach (self::POLICY_LIST as $policy) {
            $this->delete(
                '{{%abac_policy}}',
                ['AND', ['IN', 'ap_object', [$policy['ap_object']]], ['IN', 'ap_action', [$policy['ap_action']]]]
            );
        }

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
