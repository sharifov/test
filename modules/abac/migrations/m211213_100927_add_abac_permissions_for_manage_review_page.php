<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m211213_100927_add_abac_permissions_for_manage_review_page
 */
class m211213_100927_add_abac_permissions_for_manage_review_page extends Migration
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
            'ap_object' => 'email/obj/review-email',
            'ap_action' => '(manageReviewForm)|(viewReviewData)|(viewReviewEmailData)',
            'ap_action_json' => "[\"manageReviewForm\",\"viewReviewData\",\"viewReviewEmailData\"]",
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
            'email/obj/review-email',
        ]], ['IN', 'ap_action', ['(clone)']]]);

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
