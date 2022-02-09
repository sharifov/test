<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220209_102524_abac_alter_permission_for_snooze_leads
 */
class m220209_102524_abac_alter_permission_for_snooze_leads extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles) || (r.sub.snoozeCount <= 20)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"lead/lead/snooze_count","field":"snoozeCount","type":"integer","input":"text","operator":"<=","value":20}],"valid":true}',
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
    }
}
