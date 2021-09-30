<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m210928_102626_add_abac_permission_qa_task_multiple_cancel
 */
class m210928_102626_add_abac_permission_qa_task_multiple_cancel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("qa_super" in r.sub.env.user.roles || "admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["qa_super","admin"]}],"valid":true}',
            'ap_object' => 'qa-task/qa-task/act/multiple_cancel',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => '',
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
        $this->delete('{{%abac_policy}}', ['IN', 'ap_object', [
            'qa-task/qa-task/act/multiple_cancel',
        ]]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
