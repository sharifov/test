<?php

use yii\db\Migration;

/**
 * Class m200504_143957_add_index_dep_name_tb_department
 */
class m200504_143957_add_index_dep_name_tb_department extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-department-dep_name', '{{%department}}', ['dep_name']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-department-dep_name', '{{%department}}');
    }
}
