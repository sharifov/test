<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use modules\abac\src\entities\AbacMigration;
use modules\abac\src\entities\AbacPolicy;
use yii\db\Query;

/**
 * Class m220728_122043_add_abac_permission_processing_task
 *
 */
class m220728_122043_add_abac_permission_processing_task extends AbacMigration
{
    private const AP_OBJECT = 'lead/lead/task_list/processing_task';
    private const AP_ACTION = '(access)';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $abacPolicies = (new Query())
            ->from('abac_policy')
            ->andWhere(['ap_object' => 'lead/lead/task_list/assign_task', 'ap_action' => '(access)'])
            ->all();

        foreach ($abacPolicies as $policy) {
            $generateHashData = [
                self::AP_OBJECT,
                self::AP_ACTION,
                $policy['ap_subject'],
                $policy['ap_effect'],
            ];

            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => $policy['ap_subject'],
                'ap_subject_json' => json_decode($policy['ap_subject_json'], true),
                'ap_object' => self::AP_OBJECT,
                'ap_action' => self::AP_ACTION,
                'ap_action_json' => "[\"access\"]",
                'ap_effect' => $policy['ap_effect'],
                'ap_title' => 'Access to processing task list to lead',
                'ap_hash_code' => AbacService::generateHashCode($generateHashData),
                'ap_sort_order' => $policy['ap_sort_order'],
                'ap_enabled' => $policy['ap_enabled'],
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
        }

        if (count($abacPolicies) > 0) {
            AbacPolicy::deleteAll(['ap_object' => 'lead/lead/task_list/assign_task', 'ap_action' => '(access)']);
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $abacPolicies = (new Query())
            ->from('abac_policy')
            ->andWhere(['ap_object' => self::AP_OBJECT, 'ap_action' => self::AP_ACTION])
            ->all();

        foreach ($abacPolicies as $policy) {
            $generateHashData = [
                'lead/lead/task_list/assign_task',
                self::AP_ACTION,
                $policy['ap_subject'],
                $policy['ap_effect'],
            ];

            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => $policy['ap_subject'],
                'ap_subject_json' => json_decode($policy['ap_subject_json'], true),
                'ap_object' => 'lead/lead/task_list/assign_task',
                'ap_action' => self::AP_ACTION,
                'ap_action_json' => "[\"access\"]",
                'ap_effect' => $policy['ap_effect'],
                'ap_title' => 'Access to assign task list to lead',
                'ap_hash_code' => AbacService::generateHashCode($generateHashData),
                'ap_sort_order' => $policy['ap_sort_order'],
                'ap_enabled' => $policy['ap_enabled'],
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
        }

        if (count($abacPolicies) > 0) {
            AbacPolicy::deleteAll(['ap_object' => self::AP_OBJECT, 'ap_action' => self::AP_ACTION]);
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
