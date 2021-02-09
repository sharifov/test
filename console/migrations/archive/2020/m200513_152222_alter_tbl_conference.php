<?php

use yii\db\Migration;

/**
 * Class m200513_152222_alter_tbl_conference
 */
class m200513_152222_alter_tbl_conference extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('FK-conference_cf_cr_id', '{{%conference}}');
        $this->alterColumn('{{%conference}}', 'cf_cr_id', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addForeignKey('FK-conference_cf_cr_id', '{{%conference}}', ['cf_cr_id'], '{{%conference_room}}', ['cr_id'], 'CASCADE', 'CASCADE');
        $this->alterColumn('{{%conference}}', 'cf_cr_id', $this->integer()->notNull());
    }
}
