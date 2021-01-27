<?php

use yii\db\Migration;

/**
 * Class m210126_153113_add_column_ignore_unsubscribe_to_email_template_type_tbl
 */
class m210126_153113_add_column_ignore_unsubscribe_to_email_template_type_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%email_template_type}}', 'etp_ignore_unsubscribe', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%email_template_type}}', 'etp_ignore_unsubscribe');
    }
}
