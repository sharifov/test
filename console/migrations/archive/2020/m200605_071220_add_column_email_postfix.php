<?php

use yii\db\Migration;

/**
 * Class m200605_071220_add_column_email_postfix
 */
class m200605_071220_add_column_email_postfix extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%projects}}',
            'email_postfix',
            $this->string(100)->append('CHARACTER SET utf8 COLLATE utf8_unicode_ci')
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%projects}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%projects}}', 'email_postfix');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%projects}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
