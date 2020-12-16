<?php

use yii\db\Migration;

/**
 * Class m201208_101236_update_column_rank_tbl_airports
 */
class m201208_101236_update_column_rank_tbl_airports extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%airports}}', 'a_rank', $this->decimal(15, 12));
        $this->alterColumn('{{%airports}}', 'latitude', $this->decimal(18, 14));
        $this->alterColumn('{{%airports}}', 'longitude', $this->decimal(18, 14));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
//        $this->alterColumn('{{%airports}}', 'a_rank', $this->decimal(15, 12));
//        $this->alterColumn('{{%airports}}', 'latitude', $this->decimal(15, 11));
//        $this->alterColumn('{{%airports}}', 'longitude', $this->decimal(15, 11));
    }
}
