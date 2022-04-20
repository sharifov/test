<?php

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220415_110411_add_abac_base_for_shift
 */
class m220415_110411_add_abac_base_for_shift extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $isInvalidate = false;
        if (!AbacPolicy::find()->where(['ap_object' => 'shift/shift/act/user_shift_assign'])->andWhere(['ap_action' => '(access)'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
                'ap_object' => 'shift/shift/act/user_shift_assign',
                'ap_action' => '(access)',
                'ap_action_json' => "[\"access\"]",
                'ap_effect' => 1,
                'ap_title' => 'Access to UserShiftAssign',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
            $isInvalidate = true;
        }
        if (!AbacPolicy::find()->where(['ap_object' => 'shift/shift/act/my_shift_schedule'])->andWhere(['ap_action' => '(access)'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
                'ap_object' => 'shift/shift/act/my_shift_schedule',
                'ap_action' => '(access)',
                'ap_action_json' => "[\"access\"]",
                'ap_effect' => 1,
                'ap_title' => 'Access to My Shift Schedule',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
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
        if (AbacPolicy::deleteAll(['ap_object' => 'shift/shift/act/user_shift_assign', 'ap_action' => '(access)'])) {
            $isInvalidate = true;
        }
        if (AbacPolicy::deleteAll(['ap_object' => 'shift/shift/act/my_shift_schedule', 'ap_action' => '(access)'])) {
            $isInvalidate = true;
        }
        if ($isInvalidate) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
