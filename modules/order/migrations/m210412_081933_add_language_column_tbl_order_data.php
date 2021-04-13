<?php

namespace modules\order\migrations;

use yii\db\Migration;

/**
 * Class m210412_081933_add_language_column_tbl_order_data
 */
class m210412_081933_add_language_column_tbl_order_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_data}}', 'od_language_id', $this->string(5));
        $this->addForeignKey(
            'FK-order_data-od_language_id',
            '{{%order_data}}',
            'od_language_id',
            '{{%language}}',
            'language_id',
            'SET NULL',
            'CASCADE'
        );

        $this->addColumn('{{%order_data}}', 'od_market_country', $this->string(2));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'FK-order_data-od_language_id',
            '{{%order_data}}'
        );
        $this->dropColumn('{{%order_data}}', 'od_language_id');

        $this->dropColumn('{{%order_data}}', 'od_market_country');
    }
}
