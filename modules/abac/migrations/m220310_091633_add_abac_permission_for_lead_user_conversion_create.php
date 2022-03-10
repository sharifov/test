<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220310_091633_add_abac_permission_for_lead_user_conversion_create
 */
class m220310_091633_add_abac_permission_for_lead_user_conversion_create extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.closeReason == "booked_with_another_agent") || (r.sub.closeReason == "canceled_trip") || (r.sub.closeReason == "client_asked_not_to_be_contacted_again") || (r.sub.closeReason == "client_needs_no_sales") || (r.sub.closeReason == "competitor_has_a_better_contract") || (r.sub.closeReason == "proper_follow_up_done") || (r.sub.closeReason == "purchased_elsewhere") || (r.sub.closeReason == "travel_dates_passed")',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"booked_with_another_agent"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"canceled_trip"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"client_asked_not_to_be_contacted_again"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"client_needs_no_sales"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"competitor_has_a_better_contract"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"proper_follow_up_done"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"purchased_elsewhere"},{"id":"lead/lead/close_reason","field":"closeReason","type":"string","input":"select","operator":"==","value":"travel_dates_passed"}],"valid":true}',
            'ap_object' => 'lead/lead/obj/user-conversion',
            'ap_action' => '(create)',
            'ap_action_json' => "[\"create\"]",
            'ap_effect' => 1,
            'ap_title' => 'Permission to create user conversion',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%abac_policy}}', [['IN', 'ap_object', [
            'lead/lead/obj/user-conversion',
        ]], ['ap_action' => '(create)']]);
    }
}
