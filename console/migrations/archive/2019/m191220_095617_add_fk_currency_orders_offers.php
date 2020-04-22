<?php

use yii\db\Migration;

/**
 * Class m191220_095617_add_fk_currency_orders_offers
 */
class m191220_095617_add_fk_currency_orders_offers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('{{%offer}}', 'of_client_currency', $this->string(3));
        $this->addColumn('{{%offer}}', 'of_client_currency_rate', $this->decimal(8,5));
        $this->addColumn('{{%offer}}', 'of_app_total', $this->decimal(8,2));
        $this->addColumn('{{%offer}}', 'of_client_total', $this->decimal(8,2));

        $this->addForeignKey('FK-order-or_client_currency', '{{%order}}', ['or_client_currency'], '{{%currency}}', ['cur_code'], 'SET NULL', 'CASCADE');

        $this->addForeignKey('FK-offer-of_client_currency', '{{%offer}}', ['of_client_currency'], '{{%currency}}', ['cur_code'], 'SET NULL', 'CASCADE');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%offer}}');


        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-order-or_client_currency', '{{%order}}');
        $this->dropForeignKey('FK-offer-of_client_currency', '{{%offer}}');

        $this->dropColumn('{{%offer}}', 'of_client_currency');
        $this->dropColumn('{{%offer}}', 'of_client_currency_rate');
        $this->dropColumn('{{%offer}}', 'of_app_total');
        $this->dropColumn('{{%offer}}', 'of_client_total');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%offer}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
