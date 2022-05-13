<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220512_124758_change_abac_permission_closed_queue
 */
class m220512_124758_change_abac_permission_closed_queue extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update(
            '{{%abac_policy}}',
            ['ap_enabled' => 0],
            ['AND', ['IN', 'ap_object', ['lead/lead/obj/closed_queue',]], ['IN', 'ap_action', ['(access)']], ['ap_enabled' => 1]]
        );

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles) || ("agent" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"agent"}],"valid":true}',
            'ap_object' => 'lead/lead/obj/closed_queue',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => '',
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
        $this->delete(
            '{{%abac_policy}}',
            ['AND', ['IN', 'ap_object', ['lead/lead/obj/closed_queue']], ['IN', 'ap_action', ['(access)']], ['ap_enabled' => 1]]
        );

        $this->update(
            '{{%abac_policy}}',
            ['ap_enabled' => 1],
            [
                'AND',
                ['IN', 'ap_object', ['lead/lead/obj/closed_queue']],
                ['IN', 'ap_action', ['(access)']],
                ['ap_enabled' => 0]
            ]
        );

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
