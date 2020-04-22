<?php

use yii\db\Migration;

/**
 * Class m180810_123524_create_employee_contact_info
 */
class m180810_123524_create_employee_contact_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('employee_contact_info', [
            'id' => $this->primaryKey(),
            'employee_id' => $this->integer(),
            'project_id' => $this->integer(),
            'email_user' => $this->string(),
            'email_pass' => $this->string(),
            'direct_line' => $this->string(),
            'created' => $this->dateTime()->defaultExpression('NOW()'),
            'updated' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey('fk-employee_contact_info-employees', 'employee_contact_info', 'employee_id', 'employees', 'id');
        $this->addForeignKey('fk-employee_contact_info-projects', 'employee_contact_info', 'project_id', 'projects', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-employee_contact_info-employees', 'employee_contact_info');
        $this->dropForeignKey('fk-employee_contact_info-projects', 'employee_contact_info');
        $this->dropTable('employee_contact_info');
    }

}
