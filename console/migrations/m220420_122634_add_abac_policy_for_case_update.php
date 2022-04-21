<?php

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220420_122634_add_abac_policy_for_case_update
 */
class m220420_122634_add_abac_policy_for_case_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $isInvalidate = false;
        if (!AbacPolicy::find()->where(['ap_object' => 'case/case/ui/block/update'])->andWhere(['ap_subject' => '("admin" in env.user.roles) && (source_type_id == 6)'])->exists()) {
            $this->insert(
                '{{%abac_policy}}',
                [
                    'ap_rule_type' => 'p',
                    'ap_subject' => '("admin" in env.user.roles) && (source_type_id == 6)',
                    'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
                    'ap_object' => 'case/case/ui/block/update',
                    'ap_action' => '(Edit Department)|(Edit Category)|(Edit Description)',
                    'ap_action_json' => "[\"Edit Department\",\"Edit Category\",\"Edit Description\"]",
                    'ap_effect' => 0,
                    'ap_title' => 'Case - update, Deny access to edit from API',
                    'ap_sort_order' => 49,
                    'ap_enabled' => 1,
                    'ap_created_dt' => date('Y-m-d H:i:s'),
                ]
            );
            $isInvalidate = true;
        }

        if (!AbacPolicy::find()->where(['ap_object' => 'case/case/ui/block/update', 'ap_subject' => '(r.sub.env.available == true)'])->exists()) {
            $this->insert(
                '{{%abac_policy}}',
                [
                    'ap_rule_type' => 'p',
                    'ap_subject' => '(r.sub.env.available == true)',
                    'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
                    'ap_object' => 'case/case/ui/block/update',
                    'ap_action' => '(Edit Department)|(Edit Category)|(Edit Description)',
                    'ap_action_json' => "[\"Edit Department\",\"Edit Category\",\"Edit Description\"]",
                    'ap_effect' => 1,
                    'ap_title' => 'Case - update, Allow access to edit from API',
                    'ap_sort_order' => 50,
                    'ap_enabled' => 1,
                    'ap_created_dt' => date('Y-m-d H:i:s'),
                ]
            );
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
        if (AbacPolicy::deleteAll(['ap_object' => 'case/case/ui/block/update', 'ap_subject' => '("admin" in env.user.roles) && (source_type_id == 6)'])) {
            $isInvalidate = true;
        }
        if (AbacPolicy::deleteAll(['ap_object' => 'case/case/ui/block/update', 'ap_subject' => '(r.sub.env.available == true)'])) {
            $isInvalidate = true;
        }
        if ($isInvalidate) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
