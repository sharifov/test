<?php

use yii\db\Migration;

/**
 * Class m200318_160259_rename_tbl_cases_category
 */
class m200318_160259_rename_tbl_cases_category extends Migration
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

        $this->dropForeignKey(
            'FK-cases_category_cc_user_id',
            '{{%cases_category}}'
        );

        $this->dropForeignKey(
            'FK-cases_category_cc_dep_id',
            '{{%cases_category}}'
        );

        $this->dropPrimaryKey(
            'PK-cases_category_cs_key',
            '{{%cases_category}}'
        );

        $this->renameTable('{{%cases_category}}', '{{%case_category}}');

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

        $this->addForeignKey(
            'FK-case_category_cc_user_id',
            '{{%case_category}}',
            'cc_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-case_category_cc_dep_id',
            '{{%case_category}}',
            'cc_dep_id',
            '{{%department}}',
            'dep_id',
            'CASCADE',
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
            'FK-cases_cs_category',
            '{{%cases}}'
        );

        $this->dropForeignKey(
            'FK-case_category_cc_user_id',
            '{{%case_category}}'
        );

        $this->dropForeignKey(
            'FK-case_category_cc_dep_id',
            '{{%case_category}}'
        );

        $this->dropPrimaryKey(
            'PK-case_category_cs_key',
            '{{%case_category}}'
        );

        $this->renameTable('{{%case_category}}', '{{%cases_category}}');

        $this->addPrimaryKey(
            'PK-cases_category_cs_key',
            '{{%cases_category}}',
            ['cc_key']
        );

        $this->addForeignKey(
            'FK-cases_cs_category',
            '{{%cases}}',
            'cs_category',
            '{{%cases_category}}',
            'cc_key',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-cases_category_cc_user_id',
            '{{%cases_category}}',
            'cc_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-cases_category_cc_dep_id',
            '{{%cases_category}}',
            'cc_dep_id',
            '{{%department}}',
            'dep_id',
            'CASCADE',
            'CASCADE'
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%cases}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%cases_category}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
