<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%column_budy_html_from_email}}`.
 */
class m200122_115658_drop_column_body_html_from_email_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%email}}', 'e_email_body_html');

        $this->afterAction();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%email}}', 'e_email_body_html', $this->text());

        $this->afterAction();
    }

    private function afterAction()
    {
        Yii::$app->db->getSchema()->refreshTableSchema('{{%email}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
