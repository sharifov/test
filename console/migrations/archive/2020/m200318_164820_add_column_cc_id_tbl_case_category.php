<?php

use sales\entities\cases\CaseCategory;
use sales\entities\cases\Cases;
use yii\db\Migration;

/**
 * Class m200318_164820_add_column_cc_id_tbl_case_category
 */
class m200318_164820_add_column_cc_id_tbl_case_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey(
            'FK-cases_cs_category',
            '{{%cases}}'
        );

        $this->dropIndex(
            'FK-cases_cs_category',
            '{{%cases}}'
        );

        $this->dropPrimaryKey(
            'PK-case_category_cs_key',
            '{{%case_category}}'
        );

        $this->alterColumn('{{%case_category}}', 'cc_key', $this->string(50)->null()->unique());

        $this->addColumn('{{%case_category}}', 'cc_id', $this->primaryKey()->first());
        $this->addColumn('{{%cases}}', 'cs_category_id', $this->integer()->null()->after('cs_category'));

        foreach (CaseCategory::find()->all() as $category) {
            Cases::updateAll(['cs_category_id' => $category->cc_id], 'cs_category = \'' . $category->cc_key . '\'');
        }

        $this->addForeignKey(
            'FK-cases_cs_category_id',
            '{{%cases}}',
            'cs_category_id',
            '{{%case_category}}',
            'cc_id',
            'SET NULL',
            'CASCADE'
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%cases}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%case_category}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'FK-cases_cs_category_id',
            '{{%cases}}'
        );

        $this->dropColumn('{{%cases}}', 'cs_category_id');
        $this->dropColumn('{{%case_category}}', 'cc_id');

        $this->alterColumn('{{%case_category}}', 'cc_key', $this->string(50)->notNull());

        $this->addPrimaryKey(
            'PK-case_category_cs_key',
            '{{%case_category}}',
            ['cc_key']
        );

        $this->addForeignKey(
            'FK-cases_cs_category',
            '{{%cases}}',
            'cs_category',
            '{{%case_category}}',
            'cc_key',
            'SET NULL',
            'CASCADE'
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%cases}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%case_category}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
