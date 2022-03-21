<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220318_135327_add_abac_permission_for_update_multiple_notifications
 */
class m220318_135327_add_abac_permission_for_update_multiple_notifications extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.env.available == true)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'notification/notification/obj/notification/multiple-update',
            'ap_action' => '(multipleUpdateMakeRead)',
            'ap_action_json' => "[\"multipleUpdateMakeRead\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to multiple update notifications on My Notifications page',
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
            'notification/notification/obj/notification/multiple-update',
        ]], ['ap_action' => '(multipleUpdateMakeRead)']]);
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
