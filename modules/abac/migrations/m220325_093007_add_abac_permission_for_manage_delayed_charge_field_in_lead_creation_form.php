<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220325_093007_add_abac_permission_for_manage_delayed_charge_field_in_lead_creation_form
 */
class m220325_093007_add_abac_permission_for_manage_delayed_charge_field_in_lead_creation_form extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("supervision" not in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"not_in_array","value":"supervision"}],"valid":true}',
            'ap_object' => 'lead/lead/obj/lead',
            'ap_action' => '(create)',
            'ap_action_json' => "[\"create\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to manage delayed charge field in lead creation form',
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
            'lead/lead/obj/lead',
        ]], ['ap_action' => '(create)']]);
        \Yii::$app->abac->invalidatePolicyCache();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220325_093007_add_abac_permission_for_manage_delayed_charge_field_in_lead_creation_form cannot be reverted.\n";

        return false;
    }
    */
}
