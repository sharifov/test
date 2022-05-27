<?php

namespace modules\abac\migrations;

use modules\abac\src\entities\AbacPolicy;
use src\rbac\RbacMoveToAbacService;
use yii\db\Migration;

/**
 * Class m220527_052531_added_abac_permission_is_agent
 */
class m220527_052531_add_abac_permission_is_agent extends Migration
{
    private string $permissionName = 'isAgent';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        list($apSubject, $apSubjectJson) = RbacMoveToAbacService::getAbacSubjectsByRbacPermission($this->permissionName);

        if (!empty($apSubject)) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => $apSubject,
                'ap_subject_json' => $apSubjectJson,
                'ap_object' => 'lead/search/simple_search',
                'ap_action' => '(access)',
                'ap_action_json' => "[\"access\"]",
                'ap_effect' => 1,
                'ap_title' => 'Access to Simple Search Lead',
                'ap_sort_order' => 50,
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
                'ap_effect' => 1,
                'ap_title' => 'Access to Simple Search Lead',
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
        if (AbacPolicy::deleteAll(['AND', ['IN', 'ap_object', ['lead/search/simple_search']], ['IN', 'ap_action', ['(access)']]])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
