<?php

namespace modules\hotel\migrations;

use yii\db\Migration;

/**
 * Class m210212_090004_alter_tbl_hotel
 */
class m210212_090004_alter_tbl_hotel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%hotel}}', 'ph_min_price_rate', $this->decimal(10, 2));
        $this->alterColumn('{{%hotel}}', 'ph_max_price_rate', $this->decimal(10, 2));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%hotel}}', 'ph_min_price_rate', $this->tinyInteger());
        $this->alterColumn('{{%hotel}}', 'ph_max_price_rate', $this->tinyInteger());
    }
}
