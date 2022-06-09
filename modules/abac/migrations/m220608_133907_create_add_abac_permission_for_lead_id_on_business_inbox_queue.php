<?php

namespace modules\abac\migrations;

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220608_133907_create_add_abac_permission_for_lead_id_on_business_inbox_queue
 */
class m220608_133907_create_add_abac_permission_for_lead_id_on_business_inbox_queue extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $abacPolicy = new AbacPolicy();
        $abacPolicy->setAttributes([
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles) || ("supervision" in r.sub.env.user.roles) || ("sales_senior" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"supervision"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"sales_senior"}],"valid":true}',
            'ap_object' => 'lead/lead/queue/business_inbox/ui/queue_column',
            'ap_action' => '(column_lead_id)',
            'ap_action_json' => "[\"column_lead_id\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to view lead id on business inbox queue',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);
        $abacPolicy->save();

        \Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (AbacPolicy::deleteAll(['AND', ['IN', 'ap_object', ['lead/lead/queue/business_inbox/ui/queue_column',]], ['IN', 'ap_action', ['(column_lead_id)']]])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
