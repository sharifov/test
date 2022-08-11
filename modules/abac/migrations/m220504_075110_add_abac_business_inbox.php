<?php

namespace modules\abac\migrations;

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220504_075110_add_abac_business_inbox
 */
class m220504_075110_add_abac_business_inbox extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $isInvalidatePolicyCache = false;
        if (!AbacPolicy::find()->where(['ap_object' => 'lead/lead/queue/business_inbox/ui/button/view'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
                'ap_object' => 'lead/lead/queue/business_inbox/ui/button/view',
                'ap_action' => '(read)',
                'ap_action_json' => json_encode(['read']),
                'ap_effect' => 1,
                'ap_title' => 'Access to show View button',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
            $isInvalidatePolicyCache = true;
        }
        if (!AbacPolicy::find()->where(['ap_object' => 'lead/lead/queue/business_inbox/ui/queue_column'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
                'ap_object' => 'lead/lead/queue/business_inbox/ui/queue_column',
                'ap_action' => '(column_depart)|(column_segments)|(column_source_id)|(column_request_ip)|(column_client_time)|(column_pending_time)|(column_location)',
                'ap_action_json' => json_encode(['column_depart', 'column_segments', 'column_source_id', 'column_request_ip', 'column_client_time', 'column_pending_time', 'column_location']),
                'ap_effect' => 1,
                'ap_title' => 'Access to show columns',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
            $isInvalidatePolicyCache = true;
        }
        if ($isInvalidatePolicyCache) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (AbacPolicy::deleteAll(['IN', 'ap_object', ['lead/lead/queue/business_inbox/ui/button/view', 'lead/lead/queue/business_inbox/ui/queue_column']])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
