<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m211223_092512_add_policy_user_update_form_fields
 */
class m211223_092512_add_policy_user_update_form_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("username" in r.sub.formMultiAttribute || "email" in r.sub.formMultiAttribute || "full_name" in r.sub.formMultiAttribute || "password" in r.sub.formMultiAttribute || "nickname" in r.sub.formMultiAttribute || "form_roles" in r.sub.formMultiAttribute || "status" in r.sub.formMultiAttribute || "user_groups" in r.sub.formMultiAttribute || "user_projects" in r.sub.formMultiAttribute || "user_departments" in r.sub.formMultiAttribute || "client_chat_user_channel" in r.sub.formMultiAttribute || "up_work_start_tm" in r.sub.formMultiAttribute || "up_work_minutes" in r.sub.formMultiAttribute || "up_timezone" in r.sub.formMultiAttribute || "up_base_amount" in r.sub.formMultiAttribute || "up_commission_percent" in r.sub.formMultiAttribute || "up_bonus_active" in r.sub.formMultiAttribute || "up_leaderboard_enabled" in r.sub.formMultiAttribute)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"user/user/formMultiAttribute","field":"formMultiAttribute","type":"string","input":"select","operator":"contains","value":["username","email","full_name","password","nickname","form_roles","status","user_groups","user_projects","user_departments","client_chat_user_channel","up_work_start_tm","up_work_minutes","up_timezone","up_base_amount","up_commission_percent","up_bonus_active","up_leaderboard_enabled"]}],"valid":true}',
            'ap_object' => 'user/user/form/user_update',
            'ap_action' => '(view)',
            'ap_action_json' => "[\"view\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles) && ("username" in r.sub.formMultiAttribute || "email" in r.sub.formMultiAttribute || "full_name" in r.sub.formMultiAttribute || "password" in r.sub.formMultiAttribute || "nickname" in r.sub.formMultiAttribute || "form_roles" in r.sub.formMultiAttribute || "status" in r.sub.formMultiAttribute || "user_groups" in r.sub.formMultiAttribute || "user_projects" in r.sub.formMultiAttribute || "user_departments" in r.sub.formMultiAttribute || "client_chat_user_channel" in r.sub.formMultiAttribute || "up_work_start_tm" in r.sub.formMultiAttribute || "up_work_minutes" in r.sub.formMultiAttribute || "up_timezone" in r.sub.formMultiAttribute || "up_base_amount" in r.sub.formMultiAttribute || "up_commission_percent" in r.sub.formMultiAttribute || "up_bonus_active" in r.sub.formMultiAttribute || "up_leaderboard_enabled" in r.sub.formMultiAttribute)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"user/user/formMultiAttribute","field":"formMultiAttribute","type":"string","input":"select","operator":"contains","value":["username","email","full_name","password","nickname","form_roles","status","user_groups","user_projects","user_departments","client_chat_user_channel","up_work_start_tm","up_work_minutes","up_timezone","up_base_amount","up_commission_percent","up_bonus_active","up_leaderboard_enabled"]}],"valid":true}',
            'ap_object' => 'user/user/form/user_update',
            'ap_action' => '(edit)',
            'ap_action_json' => "[\"edit\"]",
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
        $this->delete('{{%abac_policy}}', ['AND', ['IN', 'ap_object', [
            'user/user/form/user_update',
        ]], ['IN', 'ap_action', ['(view)', '(edit)']]]);

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
