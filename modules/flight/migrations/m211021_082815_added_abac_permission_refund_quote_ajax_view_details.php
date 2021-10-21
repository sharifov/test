<?php

namespace modules\flight\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m211021_082815_added_abac_permission_refund_quote_ajax_view_details
 */
class m211021_082815_added_abac_permission_refund_quote_ajax_view_details extends Migration
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
            'ap_object' => 'product-quote/product-quote/act/refund_quote/details',
            'ap_action' => '(access)',
            'ap_action_json' => "[\"access\"]",
            'ap_effect' => 1,
            'ap_title' => 'Access to view refund quote details',
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
            'product-quote/product-quote/act/refund_quote/details'
        ]]);
        Yii::$app->abac->invalidatePolicyCache();
    }
}
