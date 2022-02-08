<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220208_122646_abac_create_new_permission_for_move_lead_to_snooze_from_ui
 */
class m220208_122646_abac_create_new_permission_for_move_lead_to_snooze_from_ui extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.snoozeCount >= 10) && ("agent" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"lead/lead/snooze_count","field":"snoozeCount","type":"integer","input":"text","operator":">=","value":10},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"agent"}],"valid":true}',
            'ap_object' => 'lead/lead/obj/lead',
            'ap_action' => '(snooze)',
            'ap_action_json' => "[\"snooze\"]",
            'ap_effect' => 0,
            'ap_title' => 'Access to change lead status to snooze on lead view page ',
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
            'lead/lead/obj/lead',
        ]]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
