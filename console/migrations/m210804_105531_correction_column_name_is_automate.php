<?php

use yii\db\Migration;

/**
 * Class m210804_105531_correction_column_name_is_automate
 */
class m210804_105531_correction_column_name_is_automate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('IND-cases-is_automate', '{{%cases}}');
        $this->dropColumn('{{%cases}}', 'is_automate');

        $this->addColumn('{{%cases}}', 'cs_is_automate', $this->boolean()->defaultValue(false));
        $this->createIndex('IND-cases-cs_is_automate', '{{%cases}}', ['cs_is_automate']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-cases-cs_is_automate', '{{%cases}}');
        $this->dropColumn('{{%cases}}', 'cs_is_automate');

        $this->addColumn('{{%cases}}', 'is_automate', $this->boolean()->defaultValue(false));
        $this->createIndex('IND-cases-is_automate', '{{%cases}}', ['is_automate']);
    }
}
