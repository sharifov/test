<?php

use yii\db\Migration;

/**
 * Class m190805_073341_add_column_alternative_tbl_quotes
 */
class m190805_073341_add_column_alternative_tbl_quotes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%quotes}}', 'alternative', $this->boolean()->defaultValue(false));
        Yii::$app->db->getSchema()->refreshTableSchema('{{%quotes}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%quotes}}', 'alternative');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%quotes}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }



}
