<?php

use yii\db\Migration;

/**
 * Class m200907_121354_add_column_conference_sid_tbl_conference_participant
 */
class m200907_121354_add_column_conference_sid_tbl_conference_participant extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%conference_participant}}', 'cp_cf_sid', $this->string(34));
        $this->addColumn('{{%conference_participant}}', 'cp_user_id', $this->integer());

        $this->createIndex('IND-conference_participant-cp_cf_sid', '{{%conference_participant}}', ['cp_cf_sid']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-conference_participant-cp_cf_sid', '{{%conference_participant}}');
        $this->dropColumn('{{%conference_participant}}', 'cp_cf_sid');
        $this->dropColumn('{{%conference_participant}}', 'cp_user_id');
    }
}
