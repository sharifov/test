<?php

use yii\db\Migration;

/**
 * Class m181129_132742_alter_user_group_add_processing_fee
 */
class m181129_132742_alter_user_group_add_processing_fee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_group}}', 'ug_processing_fee', $this->integer(4)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_group}}', 'ug_processing_fee');
    }

}
