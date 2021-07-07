<?php

namespace modules\abac\migrations;

use yii\db\Migration;
use Yii;

/**
 * Class m210707_061622_add_abac_policy_ui_menu_client_info
 */
class m210707_061622_add_abac_policy_ui_menu_client_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles) || ("sup_super" in r.sub.env.user.roles) || ("ex_super" in r.sub.env.user.roles) || (r.sub.is_owner == true) || (("supervision" in r.sub.env.user.roles) && (r.sub.has_owner == true) && (r.sub.is_common_group == true))',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"sup_super"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"ex_super"},{"id":"lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true},{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"supervision"},{"id":"lead/has_owner","field":"has_owner","type":"boolean","input":"radio","operator":"==","value":true},{"id":"lead/is_common_group","field":"is_common_group","type":"boolean","input":"radio","operator":"==","value":true}]}],"valid":true}',
            'ap_object' => 'lead/ui/menu/client-info',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => 'Grant access to Menu in lead client info block',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%abac_policy}}', ['IN', 'ap_object', [
            'lead/ui/menu/client-info'
        ]]);
        Yii::$app->abac->invalidatePolicyCache();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210707_061622_add_abac_policy_ui_menu_client_info cannot be reverted.\n";

        return false;
    }
    */
}
