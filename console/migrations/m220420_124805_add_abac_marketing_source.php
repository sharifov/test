<?php

use modules\abac\src\entities\AbacPolicy;
use yii\db\Migration;

/**
 * Class m220420_124805_add_abac_marketing_source
 */
class m220420_124805_add_abac_marketing_source extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!AbacPolicy::find()->where(['ap_object' => 'lead/lead/ui/block/marketing_source'])->andWhere(['ap_action' => '(read)'])->exists()) {
            $this->insert('{{%abac_policy}}', [
                'ap_rule_type' => 'p',
                'ap_subject' => '("admin" in r.sub.env.user.roles)',
                'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
                'ap_object' => 'lead/lead/ui/block/marketing_source',
                'ap_action' => '(read)',
                'ap_action_json' => "[\"read\"]",
                'ap_effect' => 1,
                'ap_title' => 'Access to show Marketing source',
                'ap_sort_order' => 50,
                'ap_enabled' => 1,
                'ap_created_dt' => date('Y-m-d H:i:s'),
            ]);
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (AbacPolicy::deleteAll(['ap_object' => 'lead/lead/ui/block/marketing_source', 'ap_action' => '(read)'])) {
            \Yii::$app->abac->invalidatePolicyCache();
        }
    }
}
