<?php

use yii\db\Migration;

/**
 * Class m200715_144642_add_column_conference_id_tbl_call_log
 */
class m200715_144642_add_column_conference_id_tbl_call_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%call_log}}', 'cl_conference_id', $this->integer());
        $this->createIndex('IND-call_log-cl_conference_id', '{{%call_log}}', ['cl_conference_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-call_log-cl_conference_id', '{{%call_log}}');
        $this->dropColumn('{{%call_log}}', 'cl_conference_id');
    }
}
