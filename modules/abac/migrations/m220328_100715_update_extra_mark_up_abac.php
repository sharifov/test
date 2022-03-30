<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220328100715updateextramarkupabac
 */
class m220328_100715_update_extra_mark_up_abac extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $this->delete('{{%abac_policy}}', [
                'IN',
                'ap_object',
                [
                    'quote/quote/obj-extra-markup',
                ]
            ]);

            $this->insert('{{%abac_policy}}', [
                'ap_rule_type'    => 'p',
                'ap_subject'      => '(r.sub.lead_status_id in [2]) && (r.sub.quote_status_id in [1]) && ((r.sub.is_owner == true) || ("admin" in r.sub.env.user.roles) || ("supervision" in r.sub.env.user.roles))',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"quote/quote/lead_status_id","field":"lead_status_id","type":"integer","input":"select","operator":"==","value":[2]},{"id":"quote/quote/quote_status_id","field":"quote_status_id","type":"integer","input":"select","operator":"==","value":[1]},{"condition":"OR","rules":[{"id":"quote/quote/is_owner","field":"is_owner","type":"boolean","input":"select","operator":"==","value":true},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"},{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"supervision"}]}],"valid":true}',
                'ap_object'       => 'quote/quote/obj-extra-markup',
                'ap_action'       => '(update)',
                'ap_action_json'  => "[\"update\"]",
                'ap_effect'       => 1,
                'ap_title'        => '',
                'ap_sort_order'   => 50,
                'ap_enabled'      => 1,
                'ap_created_dt'   => date('Y-m-d H:i:s'),
            ]);
            \Yii::$app->abac->invalidatePolicyCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                $throwable,
                'm220328_100715_update_extra_mark_up_abac:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->delete('{{%abac_policy}}', [
                'IN',
                'ap_object',
                [
                    'quote/quote/obj-extra-markup',
                ]
            ]);
            \Yii::$app->abac->invalidatePolicyCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                $throwable,
                'm220328_100715_update_extra_mark_up_abac:safeUp:Throwable'
            );
        }
    }
}
