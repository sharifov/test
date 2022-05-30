<?php

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220526_151304_add_abac_shift_schedule_supervision_notification
 */
class m220526_151304_add_abac_shift_schedule_supervision_notification extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $isInvalidate = false;
        if (!AbacPolicy::find()->where(['ap_object' => 'shift/shift/act/send_supervision_notification'])->andWhere(['ap_action' => '(access)'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in r.sub.env.user.roles || "ex_super" in r.sub.env.user.roles || "qa_super" in r.sub.env.user.roles || "supervision" in r.sub.env.user.roles || "sup_super" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["admin","ex_super","qa_super","supervision","sup_super"]}],"valid":true}',
                'ap_object' => 'shift/shift/act/send_supervision_notification',
                'ap_action' => '(access)',
                'ap_action_json' => "[\"access\"]",
                'ap_effect' => 1,
                'ap_title' => 'Get users by role (supervision) whom send request notification',
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
        if (AbacPolicy::deleteAll(['ap_object' => 'shift/shift/act/send_supervision_notification', 'ap_action' => '(access)'])) {
            $isInvalidate = true;
        }
        if ($isInvalidate) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
