<?php

namespace modules\hotel\migrations;

use yii\db\Migration;

/**
 * Class m210225_123118_add_column_hq_origin_search_data
 */
class m210225_123118_add_column_hq_origin_search_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%hotel_quote}}', 'hq_origin_search_data', $this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%hotel_quote}}', 'hq_origin_search_data');
    }
}
