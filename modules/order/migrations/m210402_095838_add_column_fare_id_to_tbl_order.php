<?php

namespace modules\order\migrations;

use yii\db\Migration;

/**
 * Class m210402_095838_add_column_fare_id_to_tbl_order
 */
class m210402_095838_add_column_fare_id_to_tbl_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'or_fare_id', $this->string()->after('or_uid'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'or_fare_id');
    }
}
