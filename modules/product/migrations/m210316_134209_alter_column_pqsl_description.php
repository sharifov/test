<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m210316_134209_alter_column_pqsl_description
 */
class m210316_134209_alter_column_pqsl_description extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%product_quote_status_log}}', 'pqsl_description', $this->string(700));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210316_134209_alter_column_pqsl_description cannot be reverted.\n";
    }
}
