<?php
namespace modules\hotel\migrations;
use yii\base\NotSupportedException;
use yii\db\Migration;
use Yii;

/**
 * Class m200115_084039_add_column_request_hash_tbl_hotel
 */
class m200115_084039_add_column_request_hash_tbl_hotel extends Migration
{
    /**
     * @return bool|void
     * @throws NotSupportedException
     */
    public function safeUp()
    {
        $this->addColumn('{{%hotel}}', 'ph_request_hash_key', $this->string(32));
        $this->addColumn('{{%hotel_quote}}', 'hq_request_hash', $this->string(32));

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%hotel}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%hotel_quote}}');
    }

    /**
     * @return bool|void
     * @throws NotSupportedException
     */
    public function safeDown()
    {
        $this->dropColumn('{{%hotel_quote}}', 'hq_request_hash');
        $this->dropColumn('{{%hotel}}', 'ph_request_hash_key');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%hotel}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%hotel_quote}}');
    }
}
