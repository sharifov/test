<?php

use yii\db\Migration;

/**
 * Class m210128_111248_alter_tbl_quote_add_new_field
 */
class m210128_111248_alter_tbl_quote_add_new_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%quotes}}', 'agent_processing_fee', $this->float());

        Yii::$app->db->getSchema()->refreshTableSchema('{{%quotes}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%quotes}}', 'agent_processing_fee');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%quotes}}');
    }
}
