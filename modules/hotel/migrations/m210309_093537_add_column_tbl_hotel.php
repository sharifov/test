<?php

namespace modules\hotel\migrations;

use yii\db\Migration;

/**
 * Class m210309_093537_add_column_tbl_hotel
 */
class m210309_093537_add_column_tbl_hotel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%hotel}}', 'ph_holder_name', $this->string(50));
        $this->addColumn('{{%hotel}}', 'ph_holder_surname', $this->string(50));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%hotel}}', 'ph_holder_name');
        $this->dropColumn('{{%hotel}}', 'ph_holder_surname');
    }
}
