<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220309_113040_add_rbac_permission_for_user_conversion_on_lead_close
 */
class m220309_113040_add_rbac_permission_for_user_conversion_on_lead_close extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.closeReason == "booked_with_another_agent") || (r.sub.closeReason == "canceled_trip") || (r.sub.closeReason == "client_asked_not_to_be_contacted_again") || (r.sub.closeReason == "competitor_has_a_better_contract") || (r.sub.closeReason == "proper_follow_up_done") || (r.sub.closeReason == "purchased_elsewhere") || (r.sub.closeReason == "travel_dates_passed")',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"booked_with_another_agent"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"canceled_trip"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"client_asked_not_to_be_contacted_again"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"competitor_has_a_better_contract"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"proper_follow_up_done"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"purchased_elsewhere"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"travel_dates_passed"}],"valid":true}',
            'ap_object' => 'lead/lead/act/user-conversion',
            'ap_action' => '(create)',
            'ap_action_json' => "[\"create\"]",
            'ap_effect' => 1,
            'ap_title' => 'Permission to create user conversion when lead moving into close status',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.closeReason == "alternative") || (r.sub.closeReason == "invalid") || (r.sub.closeReason == "transfer")',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"alternative"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"invalid"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"transfer"}],"valid":true}',
            'ap_object' => 'lead/lead/obj/lead',
            'ap_action' => '(toQaList)',
            'ap_action_json' => "[\"toQaList\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to move lead to qa',
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
        $this->delete('{{%abac_policy}}', [['IN', 'ap_object', [
            'lead/lead/act/user-conversion',
        ]], ['ap_action' => '(create)']]);
        $this->delete('{{%abac_policy}}', [['IN', 'ap_object', [
            'lead/lead/obj/lead',
        ]], ['ap_action' => '(toQaList)']]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
