<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220216_102311_add_abac_rule_to_access_smart_search_btn
 */
class m220216_102311_add_abac_rule_to_access_smart_search_btn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'lead/lead/obj/smart_search',
            'ap_action' => '(accessSmartSearch)',
            'ap_action_json' => "[\"accessSmartSearch\"]",
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
            'lead/lead/obj/smart_search',
        ]], ['IN', 'ap_action', ['(accessSmartSearch)']]]);

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
