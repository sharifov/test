<?php

use yii\db\Migration;

/**
 * Class m191223_081024_alter_column_tbl_lead_pref_currency
 */
class m191223_081024_alter_column_tbl_lead_pref_currency extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {


        // $this->dropColumn('{{%lead_preferences}}', 'pref_currency');        $this->addColumn('{{%lead_preferences}}', 'pref_currency', $this->string(3)->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
        $this->alterColumn('{{%lead_preferences}}', 'pref_currency', $this->string(3)->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
        $this->addForeignKey('FK-lead_preferences-pref_currency', '{{%lead_preferences}}', ['pref_currency'], '{{%currency}}', ['cur_code'], 'SET NULL', 'CASCADE');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%lead_preferences}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-lead_preferences-pref_currency', '{{%lead_preferences}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%lead_preferences}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
