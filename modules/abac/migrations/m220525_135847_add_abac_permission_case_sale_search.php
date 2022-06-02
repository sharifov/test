<?php

namespace modules\abac\migrations;

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220525_135847_add_abac_permission_case_sale_search
 */
class m220525_135847_add_abac_permission_case_sale_search extends Migration
{
    private string $objectName = 'case/case/sale-search/form/sale-id';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!AbacPolicy::find()->where(['ap_object' => $this->objectName])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type'    => 'p',
                'ap_subject'      => '("admin" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
                'ap_object'       => $this->objectName,
                'ap_action'       => '(access)',
                'ap_action_json'  => "[\"access\"]",
                'ap_effect'       => 1,
                'ap_title'        => 'Access to Sale Id input in Sale Search',
                'ap_sort_order'   => 50,
                'ap_enabled'      => 1,
                'ap_created_dt'   => date('Y-m-d H:i:s'),
            ]);
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (AbacPolicy::deleteAll(['AND', ['IN', 'ap_object', [$this->objectName]], ['IN', 'ap_action', ['(access)']]])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
