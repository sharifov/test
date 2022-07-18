<?php

use modules\abac\src\AbacService;
use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220718_060944_add_abac_selected_roles_policy
 */
class m220718_060944_add_abac_selected_roles_policy extends Migration
{
    private array $policies = [
        'superAdminRestriction' => [
            'ap_rule_type' => 'p',
            'ap_subject' => '("superadmin" in r.sub.selectedRoles)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"user/user/selectedRoles","field":"selectedRoles","type":"string","input":"select","operator":"contains","value":["superadmin"]}],"not":true,"valid":true}',
            'ap_object' => 'user/user/form/user_update',
            'ap_action' => '(edit)',
            'ap_action_json' => "[\"edit\"]",
            'ap_effect' => 0,
            'ap_title' => 'Default: No one can assign Superadmins',
            'ap_sort_order' => 1,
            'ap_enabled' => 1,
        ],
        'adminRestriction' => [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.selectedRoles) && (("superadmin" not in r.sub.env.user.roles) && ("admin" not in r.sub.env.user.roles))',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"user/user/selectedRoles","field":"selectedRoles","type":"string","input":"select","operator":"contains","value":["admin"]},{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"not_in_array","value":"superadmin"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"not_in_array","value":"admin"}],"not":false}],"not":false,"valid":true}',
            'ap_object' => 'user/user/form/user_update',
            'ap_action' => '(edit)',
            'ap_action_json' => "[\"edit\"]",
            'ap_effect' => 0,
            'ap_title' => 'Default: No one beside Superadmin and Administrators can assign Administrators',
            'ap_sort_order' => 1,
            'ap_enabled' => 1,
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $isInvalidate = false;

        foreach ($this->policies as $policy) {
            $policyExist = AbacPolicy::find()
                ->andWhere(['ap_object' => $policy['ap_object']])
                ->andWhere(['ap_action' => $policy['ap_action']])
                ->andWhere(['ap_subject' => $policy['ap_subject']])
                ->exists();
            if (!$policyExist) {
                $this->insert('{{%abac_policy}}', [
                    'ap_rule_type' => $policy['ap_rule_type'],
                    'ap_subject' => $policy['ap_subject'],
                    'ap_subject_json' => $policy['ap_subject_json'],
                    'ap_object' => $policy['ap_object'],
                    'ap_action' => $policy['ap_action'],
                    'ap_action_json' => $policy['ap_action_json'],
                    'ap_effect' => $policy['ap_effect'],
                    'ap_title' => $policy['ap_title'],
                    'ap_sort_order' => $policy['ap_sort_order'],
                    'ap_enabled' => $policy['ap_enabled'],
                    'ap_created_dt' => date('Y-m-d H:i:s'),
                    'ap_hash_code' => AbacService::generateHashCode([
                        $policy['ap_object'],
                        $policy['ap_action'],
                        $policy['ap_subject'],
                        $policy['ap_effect']
                    ])
                ]);
                $isInvalidate = true;
            }
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
        foreach ($this->policies as $policy) {
            $deletedPolicy = AbacPolicy::deleteAll([
                'AND',
                ['IN', 'ap_object', [$policy['ap_object']]],
                ['IN', 'ap_action', [$policy['ap_action']]],
                ['IN', 'ap_subject', [$policy['ap_subject']]],
            ]);
            if ($deletedPolicy) {
                \Yii::$app->abac->invalidatePolicyCache();
            }
        }
    }
}
