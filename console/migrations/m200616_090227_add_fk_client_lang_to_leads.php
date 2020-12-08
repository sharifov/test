<?php

use yii\db\Migration;

/**
 * Class m200616_090227_add_fk_client_lang_to_leads
 */
class m200616_090227_add_fk_client_lang_to_leads extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\base\NotSupportedException
     */
    public function safeUp()
    {
        $this->addForeignKey(
            'FK-leads-l_client_lang',
            '{{%leads}}',
            'l_client_lang',
            '{{%language}}',
            'language_id',
            'SET NULL',
            'CASCADE'
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%leads}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     * @throws \yii\base\NotSupportedException
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-leads-l_client_lang', '{{%leads}}');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%leads}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
