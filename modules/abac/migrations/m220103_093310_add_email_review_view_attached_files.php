<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220103_093310_add_email_review_view_attached_files
 */
class m220103_093310_add_email_review_view_attached_files extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.env.available == true)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'email/obj/review-email',
            'ap_action' => '(viewReviewEmailAttachedFiles)',
            'ap_action_json' => "[\"viewReviewEmailAttachedFiles\"]",
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
    }
}
