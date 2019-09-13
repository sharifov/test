<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%call}}`.
 */
class m190911_062701_add_columns_to_call_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%call}}', 'c_offset_gmt', $this->string(100)->defaultValue(null));
        $this->addColumn('{{%call}}', 'c_from_country', $this->string(50)->defaultValue(null));
        $this->addColumn('{{%call}}', 'c_from_state', $this->string(50)->defaultValue(null));
        $this->addColumn('{{%call}}', 'c_from_city', $this->string(50)->defaultValue(null));

        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%call}}', 'c_offset_gmt');
        $this->dropColumn('{{%call}}', 'c_from_country');
        $this->dropColumn('{{%call}}', 'c_from_state');
        $this->dropColumn('{{%call}}', 'c_from_city');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
