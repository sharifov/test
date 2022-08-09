<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220805_143509_remove_all_abac_permissions_for_smart_lead_distribution
 */
class m220805_143509_remove_all_abac_permissions_for_smart_lead_distribution extends Migration
{
    private const POLICY_LIST = [
        'smartLeadDistribution/business_leads/first_category',
        'smartLeadDistribution/business_leads/second_category',
        'smartLeadDistribution/business_leads/third_category',
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach (self::POLICY_LIST as $policy) {
            $this->delete(
                '{{%abac_policy}}',
                ['ap_object' => $policy]
            );
        }

        \Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220805_143509_remove_all_abac_permissions_for_smart_lead_distribution cannot be reverted.\n";

        return true;
    }
}
