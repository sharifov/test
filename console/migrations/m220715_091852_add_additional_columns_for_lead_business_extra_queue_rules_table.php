<?php

use yii\db\Migration;

/**
 * Class m220715_091852_add_additional_columns_for_lead_business_extra_queue_rules_table
 */
class m220715_091852_add_additional_columns_for_lead_business_extra_queue_rules_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $this->addColumn('{{%lead_business_extra_queue_rules}}', '[[lbeqr_duration]]', $this->integer()->notNull());
            $this->addColumn('{{%lead_business_extra_queue_rules}}', '[[lbeqr_start_time]]', $this->string()->notNull());
            $this->addColumn('{{%lead_business_extra_queue_rules}}', '[[lbeqr_end_time]]', $this->string()->notNull());
            $this->addColumn('{{%lead_business_extra_queue_rules}}', '[[lbeqr_enabled]]', $this->boolean()->notNull()->defaultValue(false));
            \Yii::$app->db->getSchema()->refreshTableSchema('{{%lead_business_extra_queue_rules}}');
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220715_091852_add_additional_columns_for_lead_business_extra_queue_rules_table:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->dropColumn('{{%lead_business_extra_queue_log}}', '[[lbeqr_duration]]');
            $this->dropColumn('{{%lead_business_extra_queue_log}}', '[[lbeqr_start_time]]');
            $this->dropColumn('{{%lead_business_extra_queue_log}}', '[[lbeqr_end_time]]');
            $this->dropColumn('{{%lead_business_extra_queue_log}}', '[[lbeqr_enabled]]');
            \Yii::$app->db->getSchema()->refreshTableSchema('{{%lead_business_extra_queue_rules}}');
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220715_091852_add_additional_columns_for_lead_business_extra_queue_rules_table:safeDown:Throwable'
            );
        }
    }
}
