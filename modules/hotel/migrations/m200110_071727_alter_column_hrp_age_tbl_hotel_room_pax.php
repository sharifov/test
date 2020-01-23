<?php
namespace modules\hotel\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200110_071727_alter_column_hrp_age_tbl_hotel_room_pax
 */
class m200110_071727_alter_column_hrp_age_tbl_hotel_room_pax extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->alterColumn('{{%hotel_room_pax}}', 'hrp_age', $this->tinyInteger(3)->unsigned());

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

		Yii::$app->db->getSchema()->refreshTableSchema('{{%hotel_room_pax}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->alterColumn('{{%hotel_room_pax}}', 'hrp_age', $this->tinyInteger(2));

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

		Yii::$app->db->getSchema()->refreshTableSchema('{{%hotel_room_pax}}');
	}
}
