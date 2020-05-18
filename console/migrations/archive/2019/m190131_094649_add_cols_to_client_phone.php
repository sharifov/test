<?php

use yii\db\Migration;

/**
 * Class m190131_094649_add_cols_to_client_phone
 */
class m190131_094649_add_cols_to_client_phone extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%client_phone}}', 'is_sms', $this->integer(1)->defaultValue(0));
        $this->addColumn('{{%client_phone}}', 'validate_dt', $this->dateTime()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%client_phone}}', 'is_sms');
        $this->dropColumn('{{%client_phone}}', 'validate_dt');
    }
}
