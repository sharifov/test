<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m210309_092658_alter_tbl_product_add_new_column
 */
class m210309_092658_alter_tbl_product_add_new_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'pr_gid', $this->string(32)->after('pr_id')->unique());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'pr_gid');
    }
}
