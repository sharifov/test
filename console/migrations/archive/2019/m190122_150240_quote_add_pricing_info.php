<?php

use yii\db\Migration;

/**
 * Class m190122_150240_quote_add_pricing_info
 */
class m190122_150240_quote_add_pricing_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%quotes}}', 'pricing_info', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%quotes}}', 'pricing_info');
    }

}
