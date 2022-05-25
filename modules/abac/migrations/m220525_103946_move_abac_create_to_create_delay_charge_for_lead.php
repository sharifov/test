<?php

namespace modules\abac\migrations;

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220525_103946_move_abac_create_to_create_delay_charge_for_lead
 */
class m220525_103946_move_abac_create_to_create_delay_charge_for_lead extends Migration
{
    private string $apObject = 'lead/lead/obj/lead';
    private string $apAction = '(create)';
    private string $newAction = 'createDelayCharge';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /** @var AbacPolicy $abacPolicy */
        if ($abacPolicy = AbacPolicy::find()->where(['ap_object' => $this->apObject, 'ap_action' => $this->apAction, 'ap_enabled' => 1])->one()) {
            $this->update(
                '{{%abac_policy}}',
                ['ap_enabled' => 0],
                ['AND', ['ap_object' => $this->apObject], ['ap_action' => $this->apAction]]
            );

            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => $abacPolicy->ap_rule_type,
                'ap_subject' => $abacPolicy->ap_subject,
                'ap_subject_json' => $abacPolicy->ap_subject_json,
                'ap_object' => $this->apObject,
                'ap_action' => '(' . $this->newAction . ')',
                'ap_action_json' => json_encode([$this->newAction]),
                'ap_effect' => $abacPolicy->ap_effect,
                'ap_title' => $abacPolicy->ap_title,
                'ap_sort_order' => $abacPolicy->ap_sort_order,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("supervision" not in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"not_in_array","value":"supervision"}],"valid":true}',
                'ap_object' => 'lead/lead/obj/lead',
                'ap_action' => '(' . $this->newAction . ')',
                'ap_action_json' => json_encode([$this->newAction]),
                'ap_effect' => 1,
                'ap_title' => 'Access to manage delayed charge field in lead creation form',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles) || ("client_chat_agent_expert" in r.sub.env.user.roles) || ("exchange_senior" in r.sub.env.user.roles) || ("sales_senior" in r.sub.env.user.roles) || ("supervision" in r.sub.env.user.roles) || ("support_senior" in r.sub.env.user.roles) || ("business_agent" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"client_chat_agent_expert"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"exchange_senior"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"sales_senior"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"supervision"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"support_senior"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"business_agent"}],"valid":true}',
            'ap_object' => 'lead/lead/obj/lead',
            'ap_action' => $this->apAction,
            'ap_action_json' => json_encode(['create']),
            'ap_effect' => 1,
            'ap_title' => 'Access to create lead',
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
            ['AND', ['ap_object' => $this->apObject], ['ap_action' => $this->apAction], ['ap_enabled' => 1]]
        );

        $this->delete(
            '{{%abac_policy}}',
            ['AND', ['ap_object' => $this->apObject], ['ap_action' => '(' . $this->newAction . ')']]
        );

        $this->update(
            '{{%abac_policy}}',
            ['ap_enabled' => 1],
            ['AND', ['ap_object' => $this->apObject], ['ap_action' => $this->apAction]]
        );
        \Yii::$app->abac->invalidatePolicyCache();
    }
}
