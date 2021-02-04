<?php

use yii\db\Migration;

/**
 * Class m201211_125857_add_column_cl_ip_tbl_clients
 */
class m201211_125857_add_column_cl_ip_tbl_clients extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%clients}}', 'cl_ip', $this->string(39));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%clients}}', 'cl_ip');
    }
}
