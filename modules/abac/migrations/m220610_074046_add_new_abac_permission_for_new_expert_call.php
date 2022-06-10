<?php

namespace modules\abac\migrations;

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220610_074046_add_new_abac_permission_for_new_expert_call
 */
class m220610_074046_add_new_abac_permission_for_new_expert_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.hasFlightSegment == true) && (r.sub.quoteCount > 0) && (r.sub.leadStatus == 2) && (((r.sub.canMakeCall == true) && (r.sub.callCount > 0)) || ((r.sub.canSendEmail == true) && (r.sub.emailCount > 0)) || ((r.sub.canSendSms == true) && (r.sub.smsCount > 0)))',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"lead/expert_call/hasFlightSegment","field":"hasFlightSegment","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/expert_call/quoteCount","field":"quoteCount","type":"integer","input":"text","operator":">","value":0},{"id":"lead/expert_call/leadStatus","field":"leadStatus","type":"integer","input":"select","operator":"==","value":2},{"condition":"OR","rules":[{"condition":"AND","rules":[{"id":"lead/expert_call/canMakeCall","field":"canMakeCall","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/expert_call/callCount","field":"callCount","type":"integer","input":"text","operator":">","value":0}]},{"condition":"AND","rules":[{"id":"lead/expert_call/canSendEmail","field":"canSendEmail","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/expert_call/emailCount","field":"emailCount","type":"integer","input":"text","operator":">","value":0}]},{"condition":"AND","rules":[{"id":"lead/expert_call/canSendSms","field":"canSendSms","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/expert_call/smsCount","field":"smsCount","type":"integer","input":"text","operator":">","value":0}]}]}],"valid":true}',
            'ap_object' => 'lead/expert_call/act/new_call',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to create new Expert Call',
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
        if (AbacPolicy::deleteAll(['AND', ['IN', 'ap_object', ['lead/expert_call/act/new_call',]], ['IN', 'ap_action', ['(access)']]])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
