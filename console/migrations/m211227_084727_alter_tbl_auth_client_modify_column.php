<?php

use yii\db\Migration;

/**
 * Class m211227_084727_alter_tbl_auth_client_modify_column
 */
class m211227_084727_alter_tbl_auth_client_modify_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%auth_client}}', 'ac_source', $this->tinyInteger(2)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%auth_client}}', 'ac_source', $this->string()->notNull());
    }
}
