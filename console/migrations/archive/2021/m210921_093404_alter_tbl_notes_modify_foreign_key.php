<?php

use yii\db\Migration;

/**
 * Class m210921_093404_alter_tbl_notes_modify_foreign_key
 */
class m210921_093404_alter_tbl_notes_modify_foreign_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-notes-lead', '{{%notes}}');
        $this->addForeignKey('fk-notes-lead', '{{%notes}}', 'lead_id', 'leads', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-notes-lead', '{{%notes}}');
        $this->addForeignKey('fk-notes-lead', '{{%notes}}', 'lead_id', 'leads', 'id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210921_093404_alter_tbl_notes_modify_foreign_key cannot be reverted.\n";

        return false;
    }
    */
}
