<?php

use yii\db\Migration;

/**
 * Class m200605_131821_add_column_email_send_dt_tbl_case_sale
 */
class m200605_131821_add_column_email_send_dt_tbl_case_sale extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->addColumn('{{%case_sale}}', 'css_send_email_dt', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropColumn('{{%case_sale}}', 'css_send_email_dt');
    }
}
