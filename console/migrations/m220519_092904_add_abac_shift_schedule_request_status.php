<?php

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220519_092904_add_abac_shift_schedule_request_status
 */
class m220519_092904_add_abac_shift_schedule_request_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $isInvalidate = false;
        if (!AbacPolicy::find()->where(['ap_object' => 'shift/shift/obj/user_shift_request_event'])->andWhere(['ap_action' => '(access)'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("4" in r.sub.formSelectRequestStatus) && ("agent" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"shift/shift/formSelectRequestStatus","field":"formSelectRequestStatus","type":"string","input":"select","operator":"contains","value":["4"]},{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["agent"]}],"valid":true}',
                'ap_object' => 'shift/shift/obj/user_shift_request_event',
                'ap_action' => '(access)',
                'ap_action_json' => "[\"access\"]",
                'ap_effect' => 1,
                'ap_title' => 'Access agent to shift request status',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);

            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '	("1" in r.sub.formSelectRequestStatus || "2" in r.sub.formSelectRequestStatus || "3" in r.sub.formSelectRequestStatus || "4" in r.sub.formSelectRequestStatus) && ("admin" in r.sub.env.user.roles || "supervision" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"shift/shift/formSelectRequestStatus","field":"formSelectRequestStatus","type":"string","input":"select","operator":"contains","value":["1","2","3","4"]},{"id":"env_user_multi_roles","field":"env.user.roles","type":"string","input":"select","operator":"contains","value":["admin","supervision"]}],"valid":true}',
                'ap_object' => 'shift/shift/obj/user_shift_request_event',
                'ap_action' => '(access)',
                'ap_action_json' => "[\"access\"]",
                'ap_effect' => 1,
                'ap_title' => 'Access admin and supervision to shift request status',
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
        if (AbacPolicy::deleteAll(['ap_object' => 'shift/shift/obj/user_shift_request_event', 'ap_action' => '(access)'])) {
            $isInvalidate = true;
        }
        if ($isInvalidate) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
