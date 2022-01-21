<?php

use yii\db\Migration;

/**
 * Class m220110_150655_alter_columns_png_lenght
 */
class m220110_150655_alter_columns_png_lenght extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%case_sale}}', 'css_sale_pnr', $this->string(70));
        $this->alterColumn('{{%flight_quote_flight}}', 'fqf_pnr', $this->string(70));
        $this->alterColumn('{{%flight_quote_booking}}', 'fqb_pnr', $this->string(70));

        Yii::$app->db->getSchema()->refreshTableSchema('{{%case_sale}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220110_150655_alter_columns_png_lenght cannot be reverted.\n";
    }
}
