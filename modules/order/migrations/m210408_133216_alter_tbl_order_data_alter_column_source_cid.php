<?php

namespace modules\order\migrations;

use common\models\Sources;
use modules\order\src\entities\orderData\OrderData;
use yii\db\Migration;

/**
 * Class m210408_133216_alter_tbl_order_data_alter_column_source_cid
 */
class m210408_133216_alter_tbl_order_data_alter_column_source_cid extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_data}}', 'od_source_id', $this->integer()->after('od_display_uid'));
        $this->addForeignKey('FK-order_data-od_source_id', '{{%order_data}}', 'od_source_id', '{{%sources}}', 'id', 'SET NULL', 'CASCADE');

        $orderData = OrderData::find()->all();
        foreach ($orderData as $data) {
            if ($data->od_source_cid) {
                $source = Sources::find()->byCid($data->od_source_cid)->one();
                if ($source) {
                    $data->od_source_id = $source->id;
                    $data->save();
                }
            }
        }
        $this->dropColumn('{{%order_data}}', 'od_source_cid');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%order_data}}', 'od_source_cid', $this->string(10));

        $orderData = OrderData::find()->all();
        foreach ($orderData as $data) {
            if ($data->od_source_id) {
                $source = Sources::find()->where(['id' => $data->od_source_id])->one();
                if ($source) {
                    $data->od_source_cid = $source->id;
                    $data->save();
                }
            }
        }

        $this->dropForeignKey('FK-order_data-od_source_id', '{{%order_data}}');
        $this->dropColumn('{{%order_data}}', 'od_source_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210408_133216_alter_tbl_order_data_alter_column_source_cid cannot be reverted.\n";

        return false;
    }
    */
}
