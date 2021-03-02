<?php

use yii\db\Migration;

/**
 * Class m210302_090424_add_column_comment_tbl_transaction
 */
class m210302_090424_add_column_comment_tbl_transaction extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%transaction}}', 'tr_comment', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%transaction}}', 'tr_comment');
    }
}
