<?php
namespace modules\flight\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200111_132525_alter_column_origin_iata_tbl_flight_segment
 */
class m200111_132525_alter_column_origin_iata_tbl_flight_segment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->alterColumn('{{%flight_segment}}', 'fs_origin_iata', $this->string(3)->notNull());

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->alterColumn('{{%flight_segment}}', 'fs_origin_iata', $this->tinyInteger(3)->notNull());

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}
}
