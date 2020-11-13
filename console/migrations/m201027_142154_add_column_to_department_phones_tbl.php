<?php

use yii\db\Migration;

/**
 * Class m201027_142154_add_column_to_department_phones_tbl
 */
class m201027_142154_add_column_to_department_phones_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%department_phone_project}}', 'dpp_allow_transfer', $this->boolean());
        $this->createIndex('IND-dpp_allow_transfer', '{{%department_phone_project}}', 'dpp_allow_transfer');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-dpp_allow_transfer', '{{%department_phone_project}}');
        $this->dropColumn('{{%department_phone_project}}', 'dpp_allow_transfer');
    }
}
