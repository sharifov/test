<?php

namespace modules\hotel\migrations;

use yii\db\Migration;

/**
 * Class m210216_140907_edit_hotel_quote_unique_key
 */
class m210216_140907_edit_hotel_quote_unique_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('hq_hash_key', '{{hotel_quote}}');
        $this->createIndex('UNIQUE-hq_hotel_id-hq_hash_key', '{{%hotel_quote}}', ['hq_hotel_id', 'hq_hash_key']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('UNIQUE-hq_hotel_id-hq_hash_key', '{{%hotel_quote}}');
        $this->alterColumn('{{%hotel_quote}}', 'hq_hash_key', $this->string(32)->unique());
    }
}
