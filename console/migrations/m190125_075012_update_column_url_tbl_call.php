<?php

use yii\db\Migration;

/**
 * Class m190125_075012_update_column_url_tbl_call
 */
class m190125_075012_update_column_url_tbl_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%call}}', 'c_uri', $this->string(200));
        $this->alterColumn('{{%call}}', 'c_recording_url', $this->string(200));

        $this->addColumn('{{%call}}', 'c_com_call_id', $this->integer());
        $this->addColumn('{{%call}}', 'c_updated_dt', $this->timestamp());

        $this->addColumn('{{%call}}', 'c_project_id', $this->integer());
        $this->addColumn('{{%call}}', 'c_error_message', $this->string(500));
        $this->addColumn('{{%call}}', 'c_is_new', $this->boolean()->defaultValue(false));
        $this->addColumn('{{%call}}', 'c_is_deleted', $this->boolean()->defaultValue(false));

        $this->addForeignKey('FK-call_c_project_id', '{{%call}}', ['c_project_id'], '{{%projects}}', ['id'], 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%call}}', 'c_com_call_id');
        $this->dropColumn('{{%call}}', 'c_updated_dt');

        $this->dropColumn('{{%call}}', 'c_project_id');
        $this->dropColumn('{{%call}}', 'c_error_message');
        $this->dropColumn('{{%call}}', 'c_is_new');
        $this->dropColumn('{{%call}}', 'c_is_deleted');
    }


}
