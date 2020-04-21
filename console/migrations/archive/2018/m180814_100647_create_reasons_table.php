<?php

use yii\db\Migration;

/**
 * Handles the creation of table `reasons`.
 */
class m180814_100647_create_reasons_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('reasons', [
            'id' => $this->primaryKey(),
            'employee_id' => $this->integer(),
            'lead_id' => $this->integer(),
            'reason' => $this->string(),
            'created' => $this->dateTime()->defaultExpression('NOW()')
        ]);

        $this->addForeignKey('fk-reasons-employee', 'reasons', 'employee_id', 'employees', 'id');
        $this->addForeignKey('fk-reasons-lead', 'reasons', 'lead_id', 'leads', 'id');


        $this->createTable('notes', [
            'id' => $this->primaryKey(),
            'employee_id' => $this->integer(),
            'lead_id' => $this->integer(),
            'message' => $this->text(),
            'created' => $this->dateTime()->defaultExpression('NOW()')
        ]);

        $this->addForeignKey('fk-notes-employee', 'notes', 'employee_id', 'employees', 'id');
        $this->addForeignKey('fk-notes-lead', 'notes', 'lead_id', 'leads', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-notes-employee', 'notes');
        $this->dropForeignKey('fk-notes-lead', 'notes');

        $this->dropTable('notes');

        $this->dropForeignKey('fk-reasons-employee', 'reasons');
        $this->dropForeignKey('fk-reasons-lead', 'reasons');

        $this->dropTable('reasons');
    }
}
