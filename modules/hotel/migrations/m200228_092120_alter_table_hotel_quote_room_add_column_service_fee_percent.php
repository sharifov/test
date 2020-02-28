<?php
namespace modules\hotel\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200228_092120_alter_table_hotel_quote_room_add_column_service_fee_percent
 */
class m200228_092120_alter_table_hotel_quote_room_add_column_service_fee_percent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('{{%hotel_quote_room}}', 'hqr_service_fee_percent', $this->decimal(5,2)->defaultValue(3.50));
		Yii::$app->db->getSchema()->refreshTableSchema('{{%hotel_quote_room}}');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropColumn('{{%hotel_quote_room}}', 'hqr_service_fee_percent');
		Yii::$app->db->getSchema()->refreshTableSchema('{{%hotel_quote_room}}');
	}
}
