<?php

use yii\db\Migration;

/**
 * Class m201218_114035_add_column_marketing_country_tbl_client
 */
class m201218_114035_add_column_marketing_country_tbl_client extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%clients}}', 'cl_marketing_country', $this->string(10));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%clients}}', 'cl_marketing_country');
    }
}
