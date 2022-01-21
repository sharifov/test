<?php

use yii\db\Migration;

/**
 * Class m210430_061245_add_column_provider_project_id_to_quote_tbl
 */
class m210430_061245_add_column_provider_project_id_to_quote_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%quotes}}', 'provider_project_id', $this->integer());
        $this->addForeignKey(
            'FK-quotes-provider_project_id',
            '{{%quotes}}',
            'provider_project_id',
            '{{%projects}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-quotes-provider_project_id', '{{%quotes}}');
        $this->dropColumn('{{%quotes}}', 'provider_project_id');
    }
}
