<?php

use yii\db\Migration;

/**
 * Class m190322_103038_update_fk_project
 */
class m190322_103038_update_fk_project extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->dropForeignKey('fk-project_employee_access-projects', 'project_employee_access');
        $this->dropForeignKey('fk-project_employee_access-employees', 'project_employee_access');

        $this->addForeignKey('fk-project_employee_access-projects', 'project_employee_access', 'project_id', 'projects', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk-project_employee_access-employees', 'project_employee_access', 'employee_id', 'employees', 'id', 'SET NULL', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey('fk-project_employee_access-projects', 'project_employee_access');
        $this->dropForeignKey('fk-project_employee_access-employees', 'project_employee_access');

        $this->addForeignKey('fk-project_employee_access-projects', 'project_employee_access', 'project_id', 'projects', 'id');
        $this->addForeignKey('fk-project_employee_access-employees', 'project_employee_access', 'employee_id', 'employees', 'id');

    }


}
