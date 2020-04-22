<?php
use yii\db\Migration;

/**
 * Class m181009_133650_alter_leads_for_clone
 */
class m181009_133650_alter_leads_for_clone extends Migration
{

    /**
     *
     * {@inheritdoc}
     *
     */
    public function safeUp()
    {
        $this->addColumn('{{%leads}}', 'clone_id', $this->integer()->null());
        $this->addColumn('{{%leads}}', 'description', $this->string(255)->null());
        $this->addForeignKey('fk-lead-clone', '{{%leads}}', 'clone_id', '{{%leads}}', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-lead-clone', '{{%leads}}');
        $this->dropColumn('{{%leads}}', 'clone_id');
        $this->dropColumn('{{%leads}}', 'description');
    }
}
