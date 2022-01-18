<?php

use yii\db\Migration;

/**
 * Class m210715_113812_alter_column_page_url_tbl_user_connection
 */
class m210715_113812_alter_column_page_url_tbl_user_connection extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%user_connection}}', 'uc_page_url', $this->string(1400));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
