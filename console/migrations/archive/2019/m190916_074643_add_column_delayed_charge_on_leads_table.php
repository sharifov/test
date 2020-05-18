<?php

use yii\db\Migration;

/**
 * Class m190916_074643_add_column_delayed_charge_on_leads_table
 */
class m190916_074643_add_column_delayed_charge_on_leads_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%leads}}', 'l_delayed_charge', $this->boolean()->defaultValue(false));
        Yii::$app->db->getSchema()->refreshTableSchema('{{%leads}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%leads}}', 'l_delayed_charge');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%leads}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
