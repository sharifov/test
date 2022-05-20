<?php

namespace modules\abac\migrations;

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220520_082248_add_abac_permission_for_view_shift_event_details
 */
class m220520_082248_add_abac_permission_for_view_shift_event_details extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.isEventOwner == true) || (("admin" in r.sub.env.user.roles))',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"shift/shift/isEventOwner","field":"isEventOwner","type":"boolean","input":"radio","operator":"==","value":true},{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}]}],"valid":true}',
            'ap_object' => 'shift/shift/obj/user_shift_event',
            'ap_action' => '(read)',
            'ap_action_json' => "[\"read\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to view event details',
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
        if (AbacPolicy::deleteAll(['AND', ['IN', 'ap_object', ['shift/shift/obj/user_shift_event',]], ['IN', 'ap_action', ['(read)']]])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
