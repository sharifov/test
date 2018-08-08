<?php

use yii\db\Migration;

/**
 * Handles the creation of table `acl`.
 */
class m180730_134137_create_acl_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('global_acl', [
            'id' => $this->primaryKey(),
            'mask' => $this->string()->notNull(),
            'description' => $this->string()->notNull(),
            'active' => $this->boolean()->defaultValue(true),
            'created' => $this->dateTime()->defaultExpression('NOW()'),
            'updated' => $this->dateTime()
        ]);

        $this->createTable('employee_acl', [
            'id' => $this->primaryKey(),
            'employee_id' => $this->integer(),
            'mask' => $this->string()->notNull(),
            'description' => $this->string()->notNull(),
            'active' => $this->boolean()->defaultValue(true),
            'created' => $this->dateTime()->defaultExpression('NOW()'),
            'updated' => $this->dateTime()
        ]);
        $this->addForeignKey('fk-employee_acl-employees', 'employee_acl', 'employee_id', 'employees', 'id');

        $this->createTable('employees_activity', [
            'id' =>  $this->primaryKey(),
            'employee_id' => $this->integer(),
            'user_ip' => $this->string(),
            'created' => $this->dateTime()->defaultExpression('NOW()'),
            'request' => $this->string(),
            'request_type' => $this->string(),
            'request_params' => $this->text(),
            'request_header' => $this->text(),
        ]);
        $this->addForeignKey('fk-employees_activity-employee', 'employees_activity', 'employee_id', 'employees', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('global_acl');

        $this->dropForeignKey('fk-employee_acl-employees', 'employee_acl');
        $this->dropTable('employee_acl');

        $this->dropForeignKey('fk-employees_activity-employee', 'employees_activity');
        $this->dropTable('employees_activity');
    }
}
