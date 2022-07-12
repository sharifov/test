<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lead_business_extra_queue_log}}`.
 */
class m220705_070708_create_lead_business_extra_queue_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $tableOptions = null;
            if ($this->db->driverName === 'mysql') {
                $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
            }
            $this->createTable('{{%lead_business_extra_queue_log}}', [
                'lbeql_id'            => $this->primaryKey(),
                'lbeql_lbeqr_id'      => $this->integer()->notNull(),
                'lbeql_lead_id'      => $this->integer()->notNull(),
                'lbeql_status'        => $this->integer()->unsigned()->notNull(),
                'lbeql_lead_owner_id' => $this->integer()->notNull(),
                'lbeql_created_dt'    => $this->dateTime(),
                'lbeql_updated_dt'    => $this->dateTime(),
            ], $tableOptions);
            $this->addForeignKey('FK-lead_business_extra_queue_log-lbeq_lead_id', '{{%lead_business_extra_queue_log}}', ['lbeql_lead_id'], '{{%leads}}', ['id'], 'CASCADE', 'CASCADE');
            $this->addForeignKey('FK-lead_business_extra_queue_log-lbeq_lbeqr_id', '{{%lead_business_extra_queue_log}}', ['lbeql_lbeqr_id'], '{{%lead_business_extra_queue_rules}}', ['lbeqr_id'], 'CASCADE', 'CASCADE');
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220705_070708_create_lead_business_extra_queue_log_table:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->dropTable('{{%lead_business_extra_queue_log}}');
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220705_070708_create_lead_business_extra_queue_log_table:safeDown:Throwable'
            );
        }
    }
}
