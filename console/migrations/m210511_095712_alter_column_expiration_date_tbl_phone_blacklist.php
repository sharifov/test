<?php

use yii\db\Migration;

/**
 * Class m210511_095712_alter_column_expiration_date_tbl_phone_blacklist
 */
class m210511_095712_alter_column_expiration_date_tbl_phone_blacklist extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%phone_blacklist}}', 'pbl_expiration_date', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%phone_blacklist}}', 'pbl_expiration_date', $this->date());
    }
}
