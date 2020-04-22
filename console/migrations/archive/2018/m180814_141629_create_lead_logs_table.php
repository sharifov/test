<?php

use yii\db\Migration;

/**
 * Handles the creation of table `lead_logs`.
 */
class m180814_141629_create_lead_logs_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('lead_logs', [
            'id' => $this->primaryKey(),
            'employee_id' => $this->integer(),
            'lead_id' => $this->integer(),
            'message' => $this->text(),
            'created' => $this->dateTime()->defaultExpression('NOW()')
        ]);

        $this->addForeignKey('fk-lead_logs-employee', 'lead_logs', 'employee_id', 'employees', 'id');
        $this->addForeignKey('fk-lead_logs-lead', 'lead_logs', 'lead_id', 'leads', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-lead_logs-employee', 'lead_logs');
        $this->dropForeignKey('fk-lead_logs-lead', 'lead_logs');

        $this->dropTable('lead_logs');
    }
}
