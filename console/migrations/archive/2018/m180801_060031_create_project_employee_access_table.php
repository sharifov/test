<?php

use yii\db\Migration;

/**
 * Handles the creation of table `project_employee_access`.
 */
class m180801_060031_create_project_employee_access_table extends Migration
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

        $this->createTable('project_employee_access', [
            'employee_id' => $this->integer(),
            'project_id' => $this->integer(),
            'created' => $this->dateTime()->defaultExpression('NOW()')
        ], $tableOptions);
        $this->addForeignKey('fk-project_employee_access-projects', 'project_employee_access', 'project_id', 'projects', 'id');
        $this->addForeignKey('fk-project_employee_access-employees', 'project_employee_access', 'employee_id', 'employees', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-project_employee_access-projects', 'project_employee_access');
        $this->dropForeignKey('fk-project_employee_access-employees', 'project_employee_access');
        $this->dropTable('project_employee_access');
    }
}
