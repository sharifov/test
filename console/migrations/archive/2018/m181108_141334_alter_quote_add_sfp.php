<?php

use yii\db\Migration;

/**
 * Class m181108_141334_alter_quote_add_sfp
 */
class m181108_141334_alter_quote_add_sfp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%quotes}}', 'service_fee_percent', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%quotes}}', 'service_fee_percent');
    }

}
