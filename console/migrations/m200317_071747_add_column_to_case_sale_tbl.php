<?php

use yii\db\Migration;

/**
 * Class m200317_071747_add_column_to_case_sale_tbl
 */
class m200317_071747_add_column_to_case_sale_tbl extends Migration
{
    private $table = '{{%case_sale}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->table, 'css_charged', $this->decimal(8,2));
        $this->addColumn($this->table, 'css_profit', $this->decimal(8,2));
        $this->addColumn($this->table, 'css_out_departure_airport', $this->string(10));
        $this->addColumn($this->table, 'css_out_arrival_airport', $this->string(10));
        $this->addColumn($this->table, 'css_out_date', $this->dateTime());
        $this->addColumn($this->table, 'css_in_departure_airport', $this->string(10));
        $this->addColumn($this->table, 'css_in_arrival_airport', $this->string(10));
        $this->addColumn($this->table, 'css_in_date', $this->dateTime());
        $this->addColumn($this->table, 'css_charge_type', $this->string(100));

        $this->refresh();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->table, 'css_charged');
        $this->dropColumn($this->table, 'css_profit');
        $this->dropColumn($this->table, 'css_out_departure_airport');
        $this->dropColumn($this->table, 'css_out_arrival_airport');
        $this->dropColumn($this->table, 'css_out_date');
        $this->dropColumn($this->table, 'css_in_departure_airport');
        $this->dropColumn($this->table, 'css_in_arrival_airport');
        $this->dropColumn($this->table, 'css_in_date');
        $this->dropColumn($this->table, 'css_charge_type');

        $this->refresh();
    }

    private function refresh(): void
    {
        Yii::$app->db->getSchema()->refreshTableSchema($this->table);

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }
}
