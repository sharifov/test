<?php

use yii\db\Migration;

/**
 * Class m220627_084449_correction_lead_data_key_lead_object_segment
 */
class m220627_084449_correction_lead_data_key_lead_object_segment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        \src\model\leadDataKey\entity\LeadDataKey::updateAll(
            ['ldk_is_system' => true],
            ['ldk_key' => \src\model\leadDataKey\services\LeadDataKeyDictionary::KEY_LEAD_OBJECT_SEGMENT]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        \src\model\leadDataKey\entity\LeadDataKey::updateAll(
            ['ldk_is_system' => false],
            ['ldk_key' => \src\model\leadDataKey\services\LeadDataKeyDictionary::KEY_LEAD_OBJECT_SEGMENT]
        );
    }
}
