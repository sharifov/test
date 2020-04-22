<?php

use yii\db\Migration;

/**
 * Class m190206_132205_alter_column_sms_tw_price
 */
class m190206_132205_alter_column_sms_tw_price extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%sms}}', 's_tw_price', $this->decimal(10, 5));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%sms}}', 's_tw_price', $this->string(15));
    }


}
