<?php

use yii\db\Migration;

/**
 * Class m191107_083549_add_column_type_create_tbl_lead
 */
class m191107_083549_add_column_type_create_tbl_lead extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%leads}}', 'l_type_create', $this->smallInteger());

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%leads}}', 'l_type_create');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
