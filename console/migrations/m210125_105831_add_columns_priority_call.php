<?php

use yii\db\Migration;

/**
 * Class m210125_105831_add_columns_priority_call
 */
class m210125_105831_add_columns_priority_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%department_phone_project}}', 'dpp_priority', $this->smallInteger()->defaultValue(0));
        $this->createIndex('IND-department_phone_project-priority', '{{%department_phone_project}}', ['dpp_priority']);

        $this->addColumn('{{%call_user_access}}', 'cua_priority', $this->smallInteger()->defaultValue(0));
        $this->createIndex('IND-call_user_access-priority', '{{%call_user_access}}', ['cua_priority']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-department_phone_project-priority', '{{%department_phone_project}}');
        $this->dropColumn('{{%department_phone_project}}', 'dpp_priority');

        $this->dropIndex('IND-call_user_access-priority', '{{%call_user_access}}');
        $this->dropColumn('{{%call_user_access}}', 'cua_priority');
    }
}
