<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lead_business_extra_queue_rules}}`.
 */
class m220701_073557_create_lead_business_extra_queue_rules_table extends Migration
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
            $this->createTable('{{%lead_business_extra_queue_rules}}', [
                'lbeqr_id'         => $this->primaryKey(),
                'lbeqr_key'        => $this->string(100)->notNull(),
                'lbeqr_name'        => $this->string(100)->notNull(),
                'lbeqr_description'        => $this->text()->notNull(),
                'lbeqr_params_json'        => $this->json()->notNull(),
                'lbeqr_updated_user_id' => $this->integer(),
                'lbeqr_created_dt' => $this->dateTime(),
                'lbeqr_updated_dt' => $this->dateTime(),
            ], $tableOptions);
            $this->addForeignKey('FK-lead_business_extra_queue_rules-lbeqr_updated_user_id', '{{%lead_business_extra_queue}}', ['lbeqr_updated_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220701_073557_create_lead_business_extra_queue_rules_table:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->dropTable('{{%lead_business_extra_queue_rules}}');
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220701_073557_create_lead_business_extra_queue_rules_table:safeDown:Throwable'
            );
        }
    }
}
