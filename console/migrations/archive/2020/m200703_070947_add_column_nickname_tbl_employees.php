<?php

use yii\db\Migration;

/**
 * Class m200703_070947_add_column_nickname_tbl_employees
 */
class m200703_070947_add_column_nickname_tbl_employees extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%employees}}', 'nickname', $this->string(255));
        $this->execute('UPDATE employees SET nickname = full_name');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%employees}}', 'nickname');
    }
}
