<?php

use yii\db\Migration;

/**
 * Class m211118_123322_alter_column_fs_mime_type_tbl_file_storage
 */
class m211118_123322_alter_column_fs_mime_type_tbl_file_storage extends Migration
{
    public function init()
    {
        $this->db = 'db_postgres';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%file_storage}}', 'fs_mime_type', $this->string(200));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
