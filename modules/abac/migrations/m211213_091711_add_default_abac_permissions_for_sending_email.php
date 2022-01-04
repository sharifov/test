<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m211213_091711_add_default_abac_permissions_for_sending_email
 */
class m211213_091711_add_default_abac_permissions_for_sending_email extends Migration
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
            'ap_object' => 'email/obj/preview-email',
            'ap_action' => '(send)|(sendWithoutReview)|(editMessage)|(editSubject)|(editFrom)|(editTo)|(editEmailFromName)|(editEmailToName)|(attachFiles)|(showEmailData)',
            'ap_action_json' => "[\"send\",\"sendWithoutReview\",\"editMessage\",\"editSubject\",\"editFrom\",\"editTo\",\"editEmailFromName\",\"editEmailToName\",\"attachFiles\",\"showEmailData\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to send email from email preview form',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("agent" in r.sub.env.user.roles) || ("ex_agent" in r.sub.env.user.roles) || ("ex_super" in r.sub.env.user.roles) || ("exchange_senior" in r.sub.env.user.roles) || ("qa" in r.sub.env.user.roles) || ("sales_senior" in r.sub.env.user.roles) || ("sup_agent" in r.sub.env.user.roles) || ("sup_super" in r.sub.env.user.roles) || ("supervision" in r.sub.env.user.roles) || ("support_senior" in r.sub.env.user.roles) || ("userManager" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"OR","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"agent"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"ex_agent"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"ex_super"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"exchange_senior"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"qa"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"sales_senior"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"sup_agent"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"sup_super"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"supervision"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"support_senior"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"userManager"}],"valid":true}',
            'ap_object' => 'email/obj/preview-email',
            'ap_action' => '(send)|(sendWithoutReview)|(editMessage)|(editSubject)|(attachFiles)',
            'ap_action_json' => "[\"send\",\"sendWithoutReview\",\"editMessage\",\"editSubject\",\"attachFiles\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to send email from email preview form',
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
            'email/obj/preview-email',
        ]], ['IN', 'ap_action', ['(clone)']]]);

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
