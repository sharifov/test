<?php
namespace modules\hotel\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200228_153924_alter_tbl_hotel_quote_room_add_column_system_agent_mark_up
 */
class m200228_153924_alter_tbl_hotel_quote_room_add_column_system_agent_mark_up extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('{{%hotel_quote_room}}', 'hqr_system_mark_up', $this->decimal(10,2));
		$this->addColumn('{{%hotel_quote_room}}', 'hqr_agent_mark_up', $this->decimal(10,2));
		Yii::$app->db->getSchema()->refreshTableSchema('{{%hotel_quote_room}}');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropColumn('{{%hotel_quote_room}}', 'hqr_system_mark_up');
    	$this->dropColumn('{{%hotel_quote_room}}', 'hqr_agent_mark_up');
		Yii::$app->db->getSchema()->refreshTableSchema('{{%hotel_quote_room}}');
	}
}
