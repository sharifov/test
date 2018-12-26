<?php

use yii\db\Migration;

/**
 * Class m181214_075641_kpi_history_params
 */
class m181214_075641_kpi_history_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%kpi_history}}', [
            'kh_id' => $this->primaryKey(),
            'kh_user_id' => $this->integer()->notNull(),
            'kh_date_dt' => $this->dateTime(),
            'kh_created_dt' => $this->dateTime(),
            'kh_updated_dt' => $this->dateTime(),
            'kh_agent_approved_dt' => $this->dateTime()->null(),
            'kh_super_approved_dt' => $this->dateTime()->null(),
            'kh_super_id' => $this->integer(),
            'kh_bonus_amount' => $this->decimal(10, 2)->defaultValue(0),
            'kh_bonus_active' => $this->boolean()->defaultValue(false),
            'kh_commission_percent' => $this->integer(3)->defaultValue(0),
            'kh_profit_bonus' => $this->decimal(10, 2)->defaultValue(0),
            'kh_manual_bonus' => $this->decimal(10, 2)->defaultValue(0),
            'kh_estimation_profit' => $this->decimal(10, 2)->defaultValue(0),
            'kh_description' => $this->text(),
        ], $tableOptions);

        $this->addForeignKey('fk-kh-user', '{{%kpi_history}}', 'kh_user_id', '{{%employees}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-kh-super', '{{%kpi_history}}', 'kh_super_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-kh-user', '{{%kpi_history}}');
        $this->dropForeignKey('fk-kh-super', '{{%kpi_history}}');
        $this->dropTable('{{%kpi_history}}');
    }

}
