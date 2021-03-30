<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m210329_080237_alter_tbl_product_column_lead_id
 */
class m210329_080237_alter_tbl_product_column_lead_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%product}}', 'pr_lead_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%product}}', 'pr_lead_id', $this->integer()->notNull());
    }
}
