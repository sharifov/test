<?php

use yii\db\Migration;
use src\model\leadBusinessExtraQueueRule\entity\LeadBusinessExtraQueueRule;

/**
 * Class m220725_135034_add_column_type_id_to_lead_business_extra_queue_rules_table
 */
class m220725_135034_add_column_type_id_to_lead_business_extra_queue_rules_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $this->addColumn('{{%lead_business_extra_queue_rules}}', '[[lbeqr_type_id]]', $this->integer()->notNull()->defaultValue(LeadBusinessExtraQueueRule::TYPE_ID_DEFAULT_RULE));
            \Yii::$app->db->getSchema()->refreshTableSchema('{{%lead_business_extra_queue_rules}}');
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220725_135034_add_column_type_id_to_lead_business_extra_queue_rules_table:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->dropColumn('{{%lead_business_extra_queue_rules}}', '[[lbeqr_type_id]]');
            \Yii::$app->db->getSchema()->refreshTableSchema('{{%lead_business_extra_queue_rules}}');
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220725_135034_add_column_type_id_to_lead_business_extra_queue_rules_table:safeDown:Throwable'
            );
        }
    }
}
