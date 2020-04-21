<?php

use yii\db\Migration;

/**
 * Class m190213_120005_alter_column_bo_flight_id_tbl_lead
 */
class m190213_120005_alter_column_bo_flight_id_tbl_lead extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->alterColumn('{{%leads}}', 'bo_flight_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%leads}}', 'bo_flight_id', $this->string(255));
    }


}
