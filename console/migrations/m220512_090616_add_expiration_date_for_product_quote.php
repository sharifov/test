<?php

use yii\db\Migration;

/**
 * Class m220512_090616_add_expiration_date_for_product_quote
 */
class m220512_090616_add_expiration_date_for_product_quote extends Migration
{
    private const TABLE = '{{%product_quote%}}';
    private const COLUM = 'pq_expiration_dt';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            self::TABLE,
            self::COLUM,
            $this->dateTime()->defaultValue(null)->after('pq_updated_dt')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(self::TABLE, self::COLUM);
    }
}
