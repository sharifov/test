<?php

use yii\db\Migration;

/**
 * Class m200624_121841_update_column_mask_tbl_employee_acl
 */
class m200624_121841_update_column_mask_tbl_employee_acl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%employee_acl}}', 'mask', $this->string(39)->notNull());

        Yii::$app->db->getSchema()->refreshTableSchema('{{%employee_acl}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%employee_acl}}', 'mask', $this->string(15)->notNull());

        Yii::$app->db->getSchema()->refreshTableSchema('{{%employee_acl}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }


}
