<?php

namespace modules\abac\migrations;

use yii\db\Migration;
use Yii;

/**
 * Class m210712_091116_add_abac_policy_for_fields_in_forms_client_info_block
 */
class m210712_091116_add_abac_policy_for_fields_in_forms_client_info_block extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles) || ("supervision" in r.sub.env.user.roles) || ("ex_super" in r.sub.env.user.roles) || ("sup_super" in r.sub.env.user.roles) || ("qa_super" in r.sub.env.user.roles) || (r.sub.is_owner == true)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"supervision"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"ex_super"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"sup_super"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"qa_super"},{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'lead/lead/ui/field/phone',
            'ap_action' => '(create)',
            'ap_action_json' => "[\"create\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles) || ("supervision" in r.sub.env.user.roles) || ("ex_super" in r.sub.env.user.roles) || ("sup_super" in r.sub.env.user.roles) || ("qa_super" in r.sub.env.user.roles) || (r.sub.is_owner == true)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"supervision"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"ex_super"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"sup_super"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"qa_super"},{"id":"lead/lead/is_owner","field":"is_owner","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'lead/lead/ui/field/email',
            'ap_action' => '(create)',
            'ap_action_json' => "[\"create\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'lead/lead/ui/field/locale',
            'ap_action' => '(update)',
            'ap_action_json' => "[\"update\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'lead/lead/ui/field/marketing_country',
            'ap_action' => '(update)',
            'ap_action_json' => "[\"update\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'lead/lead/ui/field/email',
            'ap_action' => '(update)',
            'ap_action_json' => "[\"update\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'lead/lead/ui/field/phone',
            'ap_action' => '(update)',
            'ap_action_json' => "[\"update\"]",
            'ap_effect' => 1,
            'ap_title' => '',
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
            'lead/lead/ui/field/phone',
            'lead/lead/ui/field/email',
            'lead/lead/ui/field/locale',
            'lead/lead/ui/field/marketing_country'
        ]]);
        Yii::$app->abac->invalidatePolicyCache();
    }
}
