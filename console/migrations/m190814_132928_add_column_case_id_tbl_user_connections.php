<?php

use yii\db\Migration;

/**
 * Class m190814_132928_add_column_case_id_tbl_user_connections
 */
class m190814_132928_add_column_case_id_tbl_user_connections extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_connection}}', 'uc_case_id', $this->integer());
        $this->addForeignKey('FK-user_connection_uc_case_id', '{{%user_connection}}', ['uc_case_id'], '{{%cases}}', ['cs_id'], 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-user_connection_uc_case_id', '{{%user_connection}}');
        $this->dropColumn('{{%user_connection}}', 'FK-user_connection_uc_case_id');
    }

}
