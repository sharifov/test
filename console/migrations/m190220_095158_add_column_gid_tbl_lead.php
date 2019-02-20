<?php

use yii\db\Migration;

/**
 * Class m190220_095158_add_column_gid_tbl_lead
 */
class m190220_095158_add_column_gid_tbl_lead extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%leads}}', 'gid', $this->string(32)->unique());
        $this->createIndex('IND-leads_gii','{{%leads}}', 'gid', true);
        $this->execute('UPDATE leads SET gid = MD5(id) WHERE gid IS NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-leads_gii', '{{%leads}}');
        $this->dropColumn('{{%leads}}', 'gid');
    }


}
