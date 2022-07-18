<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%lead_business_extra_queue_log}}`.
 */
class m220713_073925_add_description_column_to_lead_business_extra_queue_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $this->addColumn('{{%lead_business_extra_queue_log}}', '[[lbeql_description]]', $this->text());
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220713_073925_add_description_column_to_lead_business_extra_queue_log_table:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->dropColumn('{{%lead_business_extra_queue_log}}', '[[lbeql_description]]');
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220713_073925_add_description_column_to_lead_business_extra_queue_log_table:safeDown:Throwable'
            );
        }
    }
}
