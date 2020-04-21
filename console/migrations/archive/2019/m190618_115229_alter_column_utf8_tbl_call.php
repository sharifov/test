<?php

use yii\db\Migration;

/**
 * Class m190618_115229_alter_column_utf8_tbl_call
 */
class m190618_115229_alter_column_utf8_tbl_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%sms}}', 's_sms_text', $this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }

}
