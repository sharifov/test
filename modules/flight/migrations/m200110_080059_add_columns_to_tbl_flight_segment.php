<?php
namespace modules\flight\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200110_080059_add_columns_to_tbl_flight_segment
 */
class m200110_080059_add_columns_to_tbl_flight_segment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('{{%flight_segment}}', 'fs_origin_iata_label', $this->string(255));
		$this->addColumn('{{%flight_segment}}', 'fs_destination_iata_label', $this->string(255));

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropColumn('{{%flight_segment}}', 'fs_origin_iata_label');
    	$this->dropColumn('{{%flight_segment}}', 'fs_destination_iata_label');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }
}
