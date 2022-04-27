<?php

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220413_105942_add_abac_user_shift_assign
 */
class m220413_105942_add_abac_user_shift_assign extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!AbacPolicy::find()->where(['ap_object' => 'shift/shift/act/user_shift_assign'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
                'ap_object' => 'shift/shift/act/user_shift_assign',
                'ap_action' => '(update)',
                'ap_action_json' => "[\"update\"]",
                'ap_effect' => 1,
                'ap_title' => 'Access to edit UserShiftAssign',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (AbacPolicy::deleteAll(['ap_object' => 'shift/shift/act/user_shift_assign'])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
