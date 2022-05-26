<?php

use yii\db\Migration;

/**
 * Class m220526_064552_add_new_field_for_table_user_project_params
 */
class m220526_064552_add_new_field_for_table_user_project_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_project_params}}', 'upp_allow_transfer', $this->boolean()->defaultValue(1)->after('upp_allow_general_line'));
        $this->db->createCommand("update user_project_params set upp_allow_transfer = upp_allow_general_line where upp_allow_transfer = 1")->execute();
        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_project_params}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_project_params}}', 'upp_allow_transfer');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_project_params}}');
    }
}
