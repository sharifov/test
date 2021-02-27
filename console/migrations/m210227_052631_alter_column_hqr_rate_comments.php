<?php

use yii\db\Migration;

/**
 * Class m210227_052631_alter_column_hqr_rate_comments
 */
class m210227_052631_alter_column_hqr_rate_comments extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%hotel_quote_room}}', 'hqr_rate_comments', $this->string(1000));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210227_052631_alter_column_hqr_rate_comments cannot be reverted.\n";
    }
}
