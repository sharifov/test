<?php

use yii\db\Migration;

/**
 * Class m190624_081624_add_column_tbl_sources
 */
class m190624_081624_add_column_tbl_sources extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%sources}}', 'hidden', $this->boolean()->defaultValue(false));

        //$this->createIndex('IND-phone_number','{{%sources}}', 'phone_number', true);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%sources}}');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropColumn('{{%sources}}', 'hidden');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%sources}}');
    }


}
