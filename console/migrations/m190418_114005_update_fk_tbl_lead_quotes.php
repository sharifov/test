<?php

use yii\db\Migration;

/**
 * Class m190418_114005_update_fk_tbl_lead_quotes
 */
class m190418_114005_update_fk_tbl_lead_quotes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-quotes-leads', '{{%quotes}}');
        $this->addForeignKey('fk-quotes-leads', '{{%quotes}}', ['lead_id'], '{{%leads}}', ['id'], 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }


}
