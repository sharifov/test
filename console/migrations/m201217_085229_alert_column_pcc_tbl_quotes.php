<?php

use yii\db\Migration;

/**
 * Class m201217_085229_alert_column_pcc_tbl_quotes
 */
class m201217_085229_alert_column_pcc_tbl_quotes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%quotes}}', 'pcc', $this->string(50));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%quotes}}', 'pcc', $this->string(30));
    }
}
