<?php

use yii\db\Migration;

/**
 * Class m201218_130102_add_column_market_country_tbl_project_locale
 */
class m201218_130102_add_column_market_country_tbl_project_locale extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('{{%project_locale}}');

        $this->dropForeignKey('FK-project_locale-pl_project_id', '{{%project_locale}}');
        $this->dropForeignKey('FK-project_locale-pl_language_id', '{{%project_locale}}');
        $this->dropPrimaryKey('PK-project_locale', '{{%project_locale}}');

        $this->addColumn('{{%project_locale}}', 'pl_id', $this->primaryKey());
        $this->addColumn('{{%project_locale}}', 'pl_market_country', $this->string(2)->null());

        $this->alterColumn('{{%project_locale}}', 'pl_language_id', $this->string(5)->null());

        $this->addForeignKey(
            'FK-project_locale-pl_project_id',
            '{{%project_locale}}',
            'pl_project_id',
            '{{%projects}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-project_locale-pl_language_id',
            '{{%project_locale}}',
            'pl_language_id',
            '{{%language}}',
            'language_id',
            'CASCADE',
            'CASCADE'
        );


        $this->createIndex('IND-project_locale', '{{%project_locale}}', ['pl_project_id', 'pl_language_id', 'pl_market_country']);

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%project_locale}}', 'pl_market_country');
        $this->dropColumn('{{%project_locale}}', 'pl_id');

        $this->dropForeignKey('FK-project_locale-pl_project_id', '{{%project_locale}}');
        $this->dropForeignKey('FK-project_locale-pl_language_id', '{{%project_locale}}');
        $this->dropIndex('IND-project_locale', '{{%project_locale}}');


        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
