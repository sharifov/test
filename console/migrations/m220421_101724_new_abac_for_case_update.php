<?php

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220421_101724_new_abac_for_case_update
 */
class m220421_101724_new_abac_for_case_update extends Migration
{
    public function safeUp()
    {
        $isInvalidate = false;
        if (!AbacPolicy::find()->where(['ap_object' => 'case/case/ui/block/update'])->andWhere(['ap_subject' => '("admin" in r.sub.env.user.roles) && (r.sub.source_type_id == 6)'])->exists()) {
            $this->insert(
                '{{%abac_policy}}',
                [
                    'ap_rule_type' => 'p',
                    'ap_subject' => '("admin" in r.sub.env.user.roles) && (r.sub.source_type_id == 6)',
                    'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
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

        if ($isInvalidate) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }

    public function safeDown()
    {
        if (AbacPolicy::deleteAll(['ap_object' => 'case/case/ui/block/update', 'ap_subject' => '("admin" in r.sub.env.user.roles) && (r.sub.source_type_id == 6)'])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
