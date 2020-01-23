<?php
namespace modules\flight\migrations;

use yii\base\NotSupportedException;
use yii\db\Migration;
use Yii;

/**
 * Class m200115_103612_add_column_request_hash_tbl_flight
 */
class m200115_103612_add_column_request_hash_tbl_flight extends Migration
{
    /**
     * @return bool|void
     * @throws NotSupportedException
     */
    public function safeUp()
    {
        $this->addColumn('{{%flight}}', 'fl_request_hash_key', $this->string(32));
        $this->addColumn('{{%flight_quote}}', 'fq_request_hash', $this->string(32));

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%flight}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%flight_quote}}');
    }

    /**
     * @return bool|void
     * @throws NotSupportedException
     */
    public function safeDown()
    {
        $this->dropColumn('{{%flight_quote}}', 'fq_request_hash');
        $this->dropColumn('{{%flight}}', 'fl_request_hash_key');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%flight}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%flight_quote}}');
    }
}
