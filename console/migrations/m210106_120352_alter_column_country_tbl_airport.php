<?php

use yii\db\Migration;

/**
 * Class m210106_120352_alter_column_country_tbl_airport
 */
class m210106_120352_alter_column_country_tbl_airport extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%airports}}', 'country', $this->string(160));
        $this->alterColumn('{{%airport_lang}}', 'ail_country', $this->string(160));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%airports}}', 'country', $this->string(40));
        $this->alterColumn('{{%airport_lang}}', 'ail_country', $this->string(40));
    }
}
