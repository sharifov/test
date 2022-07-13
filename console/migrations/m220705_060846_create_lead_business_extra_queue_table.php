<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lead_business_extra_queue}}`.
 */
class m220705_060846_create_lead_business_extra_queue_table extends Migration
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
            $this->createTable('{{%lead_business_extra_queue}}', [
                'lbeq_lead_id'        => $this->integer()->notNull(),
                'lbeq_lbeqr_id'        => $this->integer()->notNull(),
                'lbeq_created_dt' => $this->dateTime(),
                'lbeq_expiration_dt' => $this->dateTime(),
            ], $tableOptions);
            $this->addPrimaryKey('PK-lbeq_lead_id-lbeq_lbeqr_id', '{{%lead_business_extra_queue}}', ['lbeq_lead_id', 'lbeq_lbeqr_id']);
            $this->addForeignKey('FK-lead_business_extra_queue-lbeq_lead_id', '{{%lead_business_extra_queue}}', ['lbeq_lead_id'], '{{%leads}}', ['id'], 'CASCADE', 'CASCADE');
            $this->addForeignKey('FK-lead_business_extra_queue-lbeq_lbeqr_id', '{{%lead_business_extra_queue}}', ['lbeq_lbeqr_id'], '{{%lead_business_extra_queue_rules}}', ['lbeqr_id'], 'CASCADE', 'CASCADE');
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220705_060846_create_lead_business_extra_queue_table:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->dropTable('{{%lead_business_extra_queue}}');
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220705_060846_create_lead_business_extra_queue_table:safeDown:Throwable'
            );
        }
    }
}
