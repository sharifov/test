<?php

use yii\db\Migration;

/**
 * Class m220105_125843_create_index_on_email_col_client_email_tbl
 */
class m220105_125843_create_index_on_email_col_client_email_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-client_email-email', '{{%client_email}}', 'email');
        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-client_email-email', '{{%client_email}}');
        Yii::$app->cache->flush();
    }
}
